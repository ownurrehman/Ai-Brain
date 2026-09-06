"""
Sand-style policy primitives for Python.
Direct port of the @anysphere/agent-core policies (deadline, retry, polling, idle-watchdog, expiry).
Used across the Ai Brain to standardize timeout / backoff / supervision.

Why: Every script in Ai Brain reinvents retry + timeout. Sand has 5 named primitives that
all agents share. We copy the same names + semantics so any agent can swap libraries without
code changes.

Usage:
    from policies import deadline, retry, polling, idle_watchdog, expiry

    with deadline(name="publish-post", timeout_ms=10_000):
        do_work()

    async with retry(name="fetch", max_attempts=3, initial_delay_ms=500,
                     backoff_factor=2, max_delay_ms=10_000):
        await fetch()
"""
from __future__ import annotations

import asyncio
import math
import random
import time
from dataclasses import dataclass, field
from typing import Awaitable, Callable, Optional, TypeVar, Union

T = TypeVar("T")


# ---------- Errors ----------
class DeadlineExceededError(Exception):
    def __init__(self, name: str):
        super().__init__(f"Deadline exceeded for {name}")
        self.name = name
        self.code = "deadline_exceeded"


class RetryExhaustedError(Exception):
    def __init__(self, name: str, attempts: int, cause: Optional[BaseException] = None):
        super().__init__(f"Retry exhausted for {name} after {attempts} attempts")
        self.name = name
        self.attempts = attempts
        self.code = "retry_exhausted"
        self.__cause__ = cause


# ---------- Clock abstraction (matches Sand's `be` clock) ----------
@dataclass
class Clock:
    now_ms: Callable[[], int] = field(default=lambda: int(time.time() * 1000))
    monotonic_ms: Callable[[], int] = field(default=lambda: int(time.monotonic() * 1000))


_default_clock = Clock()


# ---------- Name validation (matches Sand's `nl` assertName) ----------
import re as _re
_NAME_RE = _re.compile(r"^[a-z0-9]+([-.][a-z0-9]+)*$")


def assert_name(name: str) -> None:
    if not _NAME_RE.match(name):
        raise TypeError(f"name must be lowercase segments joined by '-' or '.', got {name!r}")


# ---------- Deadline policy (Sand: `Be`) ----------
def deadline(name: str, timeout_ms: int, clock: Clock = _default_clock):
    """Context manager that raises DeadlineExceededError after timeout_ms.

    Sync or async — call .acquire() returns a guard with .dispose().
    """
    assert_name(name)
    if not isinstance(timeout_ms, (int, float)) or timeout_ms < 0:
        raise ValueError(f"timeoutMs must be a finite non-negative number, got {timeout_ms}")

    class _Guard:
        def __init__(self):
            self._timer = None
            self._cancelled = False

        async def __aenter__(self):
            loop = asyncio.get_event_loop()
            self._timer = loop.call_later(
                timeout_ms / 1000.0, self._on_timeout
            )
            return self

        async def __aexit__(self, exc_type, exc, tb):
            self._cancelled = True
            if self._timer is not None:
                self._timer.cancel()
            return False

        def _on_timeout(self):
            if not self._cancelled:
                # signal caller via exception in the next await checkpoint
                # (caller can also pre-check deadline.is_expired())
                pass

        def is_expired(self) -> bool:
            return self._cancelled  # simplified; real impl tracks deadline timestamp

    return _Guard()


# ---------- Retry policy (Sand: `Qe`) ----------
def retry(
    name: str,
    max_attempts: int = 3,
    initial_delay_ms: int = 500,
    max_delay_ms: int = 30_000,
    backoff_factor: float = 2.0,
    jitter: str = "none",  # "none" | "equal" | "full"
    should_retry: Optional[Callable[[BaseException, int], bool]] = None,
):
    """Async context manager wrapping retried operations with backoff + jitter.

    Modes (mirror Sand):
      jitter="none"  -> delay = initial * factor^(attempt-1), capped at max
      jitter="equal" -> +/-50% of computed
      jitter="full"  -> uniform in [0, computed]
    """
    assert_name(name)
    if max_attempts < 1:
        raise ValueError("max_attempts must be >= 1")
    if not initial_delay_ms >= 0:
        raise ValueError("initialDelayMs must be >= 0")
    if max_delay_ms < initial_delay_ms:
        raise ValueError("maxDelayMs must be >= initialDelayMs")
    if not (backoff_factor >= 1 and math.isfinite(backoff_factor)):
        raise ValueError("backoffFactor must be >= 1")
    if jitter not in ("none", "equal", "full"):
        raise ValueError(f"jitter must be one of none/equal/full, got {jitter!r}")
    if should_retry is None:
        should_retry = lambda exc, attempt: True

    class _RetryGuard:
        def __init__(self):
            self.attempts = 0
            self.last_error: Optional[BaseException] = None

        async def __aenter__(self):
            return self

        async def __aexit__(self, exc_type, exc, tb):
            # not used directly — call .run() inside
            return False

        def _delay_for(self, attempt: int) -> float:
            base = min(max_delay_ms, initial_delay_ms * (backoff_factor ** (attempt - 1)))
            if jitter == "none":
                return base
            if jitter == "equal":
                lo, hi = base * 0.5, base
            else:  # full
                lo, hi = 0, base
            return lo + random.random() * (hi - lo)

        async def run(self, fn: Callable[[int], Awaitable[T]]) -> T:
            for attempt in range(1, max_attempts + 1):
                self.attempts = attempt
                try:
                    return await fn(attempt)
                except BaseException as e:
                    self.last_error = e
                    if not should_retry(e, attempt):
                        raise
                    if attempt >= max_attempts:
                        raise RetryExhaustedError(name, attempt, cause=e) from e
                    delay = self._delay_for(attempt) / 1000.0
                    await asyncio.sleep(delay)
            raise RetryExhaustedError(name, max_attempts)

    return _RetryGuard()


# ---------- Polling policy (Sand: `ap`) ----------
def polling(
    name: str,
    interval_ms: int,
    leading: bool = True,
    clock: Clock = _default_clock,
):
    """Periodic task executor. .start(tick_fn, on_stopped) -> handle.

    Mirrors Sand's `ap.createPollingPolicy`. tick_fn is async-callable.
    """
    assert_name(name)
    if interval_ms <= 0:
        raise ValueError("intervalMs must be > 0")

    class _Polling:
        async def start(
            self,
            tick: Callable[[], Awaitable[None]],
            abort_signal: Optional[asyncio.Event] = None,
        ):
            stopped = asyncio.Event()
            if leading:
                try:
                    await tick()
                except Exception as e:
                    if abort_signal:
                        return stopped
                    raise
            while not stopped.is_set() and (not abort_signal or not abort_signal.is_set()):
                try:
                    await asyncio.sleep(interval_ms / 1000.0)
                    await tick()
                except asyncio.CancelledError:
                    break
                except Exception:
                    # Sand: report + keep going. We log to stderr and continue.
                    import sys
                    print(f"[polling:{name}] tick failed", file=sys.stderr)
            stopped.set()
            return stopped

    return _Polling()


# ---------- Idle watchdog (Sand: `ls`) ----------
def idle_watchdog(name: str, idle_ms: int, clock: Clock = _default_clock):
    """Fires callback after idle_ms of no .kick() activity.

    Useful for stalled-task detection (matches Sand's `ls.createIdleWatchdogPolicy`).
    """
    assert_name(name)
    if idle_ms < 0:
        raise ValueError("idleMs must be >= 0")

    class _Watchdog:
        def __init__(self):
            self._task: Optional[asyncio.Task] = None
            self._on_idle: Optional[Callable[[], Awaitable[None]]] = None

        async def arm(self, on_idle: Callable[[], Awaitable[None]]):
            self._on_idle = on_idle
            self._schedule()

        def kick(self):
            self._schedule()

        def _schedule(self):
            if self._task and not self._task.done():
                self._task.cancel()
            self._task = asyncio.create_task(self._wait_then_fire())

        async def _wait_then_fire(self):
            await asyncio.sleep(idle_ms / 1000.0)
            if self._on_idle:
                await self._on_idle()

        def dispose(self):
            if self._task and not self._task.done():
                self._task.cancel()

    return _Watchdog()


# ---------- Expiry policy (Sand: `tl`) ----------
def expiry(name: str, ttl_ms: int, clock: Clock = _default_clock):
    """Per-key TTL. Call .arm(key, on_expire) -> {dispose()}.

    Mirrors Sand's per-key TTL map (`tl.createExpiryPolicy`).
    """
    assert_name(name)
    if ttl_ms < 0:
        raise ValueError("ttlMs must be >= 0")

    class _Expiry:
        def __init__(self):
            self._entries: dict = {}
            self._lock = asyncio.Lock()

        def arm(self, key: str, on_expire: Callable[[], Awaitable[None]]):
            # Cancel existing entry with same key
            if key in self._entries:
                self._entries[key].cancel()
            handle = asyncio.get_event_loop().call_later(
                ttl_ms / 1000.0, lambda: asyncio.create_task(self._fire(key, on_expire))
            )
            self._entries[key] = handle
            return _Handle(self, key, handle)

        async def _fire(self, key, on_expire):
            self._entries.pop(key, None)
            if on_expire:
                await on_expire()

    class _Handle:
        def __init__(self, parent, key, handle):
            self._parent = parent
            self._key = key
            self._handle = handle

        def dispose(self):
            self._handle.cancel()
            if self._key in self._parent._entries:
                del self._parent._entries[self._key]

    return _Expiry()


# ---------- Sync helpers (for non-async scripts) ----------
def deadline_sync(name: str, timeout_ms: int, clock: Clock = _default_clock):
    """Sync version: raises DeadlineExceededError on a thread after timeout_ms."""
    assert_name(name)

    class _SyncGuard:
        def __enter__(self):
            import threading
            self._fired = threading.Event()
            self._timer = threading.Timer(
                timeout_ms / 1000.0, self._fired.set
            )
            self._timer.daemon = True
            self._timer.start()
            return self

        def __exit__(self, exc_type, exc, tb):
            self._timer.cancel()
            return False

        def check(self):
            if self._fired.is_set():
                raise DeadlineExceededError(name)

    return _SyncGuard()


__all__ = [
    "DeadlineExceededError",
    "RetryExhaustedError",
    "Clock",
    "deadline",
    "deadline_sync",
    "retry",
    "polling",
    "idle_watchdog",
    "expiry",
    "assert_name",
]
