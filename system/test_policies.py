"""Smoke test for the policy primitives. Verifies all 5 work + error paths."""
import asyncio
import sys

sys.path.insert(0, "/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/system")
from policies import (
    DeadlineExceededError,
    RetryExhaustedError,
    deadline,
    deadline_sync,
    retry,
    polling,
    idle_watchdog,
    expiry,
    assert_name,
)


# ---- name validation ----
def test_assert_name():
    assert_name("ok-name")
    assert_name("a.b.c")
    try:
        assert_name("Bad-Name")
        assert False, "should have raised"
    except TypeError:
        pass
    print("[ok] assert_name")


# ---- deadline sync ----
def test_deadline_sync_fires():
    g = deadline_sync("test", timeout_ms=200)
    with g:
        pass  # ok
    print("[ok] deadline_sync context")


def test_deadline_sync_check():
    g = deadline_sync("test", timeout_ms=10)
    g.__enter__()
    import time
    time.sleep(0.05)
    try:
        g.check()
        assert False, "should have raised"
    except DeadlineExceededError as e:
        assert e.name == "test"
    g.__exit__(None, None, None)
    print("[ok] deadline_sync raises DeadlineExceededError")


# ---- retry ----
async def test_retry_succeeds_on_retry():
    attempts = {"n": 0}

    async def flaky(n):
        attempts["n"] = n
        if n < 3:
            raise ValueError("not yet")
        return "ok"

    g = retry(
        "flaky",
        max_attempts=5,
        initial_delay_ms=20,
        max_delay_ms=100,
        backoff_factor=2.0,
        jitter="none",
    )
    result = await g.run(flaky)
    assert result == "ok"
    assert attempts["n"] == 3
    print(f"[ok] retry succeeded after {attempts['n']} attempts")


async def test_retry_exhausted():
    async def always_fails(n):
        raise ValueError("nope")

    g = retry("fail", max_attempts=3, initial_delay_ms=10, max_delay_ms=50)
    try:
        await g.run(always_fails)
        assert False
    except RetryExhaustedError as e:
        assert e.attempts == 3
        assert e.name == "fail"
    print("[ok] retry raises RetryExhaustedError after max_attempts")


async def test_retry_should_retry_filter():
    attempts = {"n": 0}

    async def perm(n):
        attempts["n"] = n
        raise PermissionError("denied")

    g = retry(
        "filter",
        max_attempts=5,
        initial_delay_ms=10,
        max_delay_ms=50,
        should_retry=lambda exc, attempt: not isinstance(exc, PermissionError),
    )
    try:
        await g.run(perm)
        assert False
    except PermissionError:
        pass
    assert attempts["n"] == 1
    print("[ok] retry respects should_retry filter")


# ---- polling ----
async def test_polling():
    ticks = {"n": 0}

    async def tick():
        ticks["n"] += 1
        if ticks["n"] >= 3:
            raise asyncio.CancelledError()

    p = polling("tick", interval_ms=20, leading=False)
    try:
        await asyncio.wait_for(p.start(tick), timeout=0.5)
    except (asyncio.TimeoutError, asyncio.CancelledError):
        pass
    assert ticks["n"] >= 2, f"expected >=2 ticks, got {ticks['n']}"
    print(f"[ok] polling fired {ticks['n']} ticks")


# ---- idle_watchdog ----
async def test_idle_watchdog():
    fired = {"v": False}

    async def on_idle():
        fired["v"] = True

    w = idle_watchdog("idle", idle_ms=50)
    await w.arm(on_idle)
    # kick once to reset
    await asyncio.sleep(0.01)
    w.kick()
    await asyncio.sleep(0.08)
    # kicked at 10ms, would have fired at 60ms total. fired should be True
    # but we kicked AFTER arm, so we need to test the fire path differently
    # simpler: re-arm without kicking
    fired["v"] = False
    w2 = idle_watchdog("idle2", idle_ms=30)
    await w2.arm(on_idle)
    await asyncio.sleep(0.06)
    assert fired["v"], "watchdog should have fired"
    w2.dispose()
    print("[ok] idle_watchdog fires after idle_ms")


# ---- expiry ----
async def test_expiry():
    expired = {"v": False}

    async def on_expire():
        expired["v"] = True

    e = expiry("ttl", ttl_ms=40)
    handle = e.arm("k1", on_expire)
    await asyncio.sleep(0.08)
    assert expired["v"], "should have expired"
    handle.dispose()
    print("[ok] expiry fires after ttl_ms")


# ---- run all ----
async def main():
    test_assert_name()
    test_deadline_sync_fires()
    test_deadline_sync_check()
    await test_retry_succeeds_on_retry()
    await test_retry_exhausted()
    await test_retry_should_retry_filter()
    await test_polling()
    await test_idle_watchdog()
    await test_expiry()
    print("\nALL 9 TESTS PASSED")


if __name__ == "__main__":
    asyncio.run(main())
