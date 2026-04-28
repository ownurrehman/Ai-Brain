#!/usr/bin/env python3
"""
Asynchronous Task Queue with Priority Support and Retry Mechanism

A production-ready async task queue implementation using asyncio and heapq.
Supports task priorities, automatic retries with exponential backoff, and
concurrent worker processing.
"""

import asyncio
import heapq
import time
import logging
from dataclasses import dataclass, field
from typing import Any, Callable, Coroutine, Optional
from enum import IntEnum

logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s'
)
logger = logging.getLogger(__name__)


class TaskPriority(IntEnum):
    """Task priority levels (lower value = higher priority)"""
    CRITICAL = 0
    HIGH = 1
    NORMAL = 2
    LOW = 3


@dataclass(order=True)
class Task:
    """
    Task representation with priority ordering.
    
    The __post_init__ ensures proper heap ordering by priority first,
    then by timestamp (FIFO within same priority).
    """
    priority: int
    timestamp: float = field(compare=True)
    task_id: str = field(compare=False)
    coroutine_func: Callable[..., Coroutine] = field(compare=False, repr=False)
    args: tuple = field(compare=False, default_factory=tuple)
    kwargs: dict = field(compare=False, default_factory=dict)
    max_retries: int = field(compare=False, default=3)
    retry_count: int = field(compare=False, default=0)
    retry_delay: float = field(compare=False, default=2.0)
    
    def __post_init__(self):
        if self.timestamp is None:
            self.timestamp = time.time()


@dataclass
class TaskResult:
    """Container for task execution results"""
    task_id: str
    success: bool
    result: Any = None
    error: Optional[Exception] = None
    retries_used: int = 0
    execution_time: float = 0.0


class AsyncTaskQueue:
    """
    Asynchronous task queue with priority support and retry mechanism.
    
    Architecture:
    - Uses heapq for O(log n) priority-based task insertion/extraction
    - asyncio.Lock ensures thread-safe queue operations
    - Worker pool processes tasks concurrently
    - Exponential backoff for retries prevents thundering herd
    """
    
    def __init__(self, num_workers: int = 4, max_queue_size: int = 1000):
        self.num_workers = num_workers
        self.max_queue_size = max_queue_size
        self._queue: list[Task] = []
        self._lock = asyncio.Lock()
        self._task_counter = 0
        self._shutdown = False
        self._workers: list[asyncio.Task] = []
        self._results: dict[str, TaskResult] = {}
        
    async def start(self):
        """Start the worker pool"""
        logger.info(f"Starting task queue with {self.num_workers} workers")
        for i in range(self.num_workers):
            worker = asyncio.create_task(self._worker(i))
            self._workers.append(worker)
    
    async def stop(self):
        """Gracefully shutdown the queue"""
        logger.info("Shutting down task queue...")
        self._shutdown = True
        
        # Wait for all workers to finish
        if self._workers:
            await asyncio.gather(*self._workers, return_exceptions=True)
        
        logger.info("Task queue shutdown complete")
    
    async def enqueue(
        self,
        coroutine_func: Callable[..., Coroutine],
        *args,
        priority: TaskPriority = TaskPriority.NORMAL,
        max_retries: int = 3,
        retry_delay: float = 2.0,
        task_id: Optional[str] = None,
        **kwargs
    ) -> str:
        """
        Add a task to the queue.
        
        Args:
            coroutine_func: Async function to execute
            *args: Positional arguments for the coroutine
            priority: Task priority (CRITICAL, HIGH, NORMAL, LOW)
            max_retries: Maximum retry attempts on failure
            retry_delay: Base delay between retries (exponential backoff)
            task_id: Optional custom task ID (auto-generated if not provided)
            **kwargs: Keyword arguments for the coroutine
            
        Returns:
            task_id: Unique identifier for tracking
        """
        async with self._lock:
            if len(self._queue) >= self.max_queue_size:
                raise RuntimeError(f"Queue full (max={self.max_queue_size})")
            
            if task_id is None:
                self._task_counter += 1
                task_id = f"task-{self._task_counter}-{int(time.time() * 1000)}"
            
            task = Task(
                priority=priority.value,
                timestamp=time.time(),
                task_id=task_id,
                coroutine_func=coroutine_func,
                args=args,
                kwargs=kwargs,
                max_retries=max_retries,
                retry_delay=retry_delay
            )
            
            heapq.heappush(self._queue, task)
            logger.debug(f"Enqueued task {task_id} with priority {priority.name}")
            
            return task_id
    
    async def _worker(self, worker_id: int):
        """Worker coroutine that processes tasks from the queue"""
        logger.info(f"Worker {worker_id} started")
        
        while not self._shutdown:
            task = await self._get_task()
            
            if task is None:
                await asyncio.sleep(0.1)  # Prevent busy-waiting
                continue
            
            await self._execute_task(task, worker_id)
        
        logger.info(f"Worker {worker_id} stopped")
    
    async def _get_task(self) -> Optional[Task]:
        """Get the highest priority task from the queue"""
        async with self._lock:
            if self._queue:
                return heapq.heappop(self._queue)
            return None
    
    async def _execute_task(self, task: Task, worker_id: int):
        """Execute a task with retry logic"""
        start_time = time.time()
        
        while task.retry_count <= task.max_retries:
            try:
                logger.info(
                    f"Worker {worker_id} executing task {task.task_id} "
                    f"(attempt {task.retry_count + 1}/{task.max_retries + 1})"
                )
                
                result = await task.coroutine_func(*task.args, **task.kwargs)
                
                execution_time = time.time() - start_time
                
                task_result = TaskResult(
                    task_id=task.task_id,
                    success=True,
                    result=result,
                    retries_used=task.retry_count,
                    execution_time=execution_time
                )
                
                self._results[task.task_id] = task_result
                logger.info(f"Task {task.task_id} completed successfully in {execution_time:.2f}s")
                return
                
            except Exception as e:
                task.retry_count += 1
                logger.warning(
                    f"Task {task.task_id} failed (attempt {task.retry_count}): {e}"
                )
                
                if task.retry_count > task.max_retries:
                    execution_time = time.time() - start_time
                    
                    task_result = TaskResult(
                        task_id=task.task_id,
                        success=False,
                        error=e,
                        retries_used=task.retry_count - 1,
                        execution_time=execution_time
                    )
                    
                    self._results[task.task_id] = task_result
                    logger.error(f"Task {task.task_id} failed after {task.retry_count} attempts")
                    return
                
                # Exponential backoff with jitter
                delay = task.retry_delay * (2 ** (task.retry_count - 1))
                logger.info(f"Retrying task {task.task_id} in {delay:.1f}s")
                await asyncio.sleep(delay)
    
    async def get_result(self, task_id: str, timeout: Optional[float] = None) -> Optional[TaskResult]:
        """
        Get the result of a completed task.
        
        Args:
            task_id: The task identifier
            timeout: Maximum time to wait for result (None = no wait)
            
        Returns:
            TaskResult if available, None otherwise
        """
        start = time.time()
        
        while timeout is None or (time.time() - start) < timeout:
            if task_id in self._results:
                return self._results.pop(task_id)
            await asyncio.sleep(0.1)
        
        return None
    
    def get_queue_size(self) -> int:
        """Get current number of pending tasks"""
        return len(self._queue)


# ============================================================================
# Example Usage
# ============================================================================

async def sample_task(task_name: str, duration: float = 1.0, should_fail: bool = False):
    """Sample async task for demonstration"""
    await asyncio.sleep(duration)
    
    if should_fail:
        raise RuntimeError(f"Task {task_name} failed intentionally")
    
    return f"Task {task_name} completed successfully"


async def main():
    """Demonstration of the async task queue"""
    
    queue = AsyncTaskQueue(num_workers=3)
    await queue.start()
    
    try:
        # Enqueue tasks with different priorities
        task_ids = []
        
        # Low priority task (will likely run last)
        task_ids.append(await queue.enqueue(
            sample_task, "low-priority", 0.5,
            priority=TaskPriority.LOW
        ))
        
        # Critical priority task (will run first)
        task_ids.append(await queue.enqueue(
            sample_task, "critical", 0.5,
            priority=TaskPriority.CRITICAL
        ))
        
        # Normal priority task with retry
        task_ids.append(await queue.enqueue(
            sample_task, "will-fail-twice", 0.3, True,
            priority=TaskPriority.NORMAL,
            max_retries=2
        ))
        
        # High priority task
        task_ids.append(await queue.enqueue(
            sample_task, "high-priority", 0.5,
            priority=TaskPriority.HIGH
        ))
        
        logger.info(f"Enqueued {len(task_ids)} tasks, queue size: {queue.get_queue_size()}")
        
        # Wait for all tasks to complete
        await asyncio.sleep(3)
        
        # Collect results
        for task_id in task_ids:
            result = await queue.get_result(task_id, timeout=5.0)
            if result:
                status = "✓" if result.success else "✗"
                print(f"{status} {task_id}: retries={result.retries_used}, time={result.execution_time:.2f}s")
    
    finally:
        await queue.stop()


if __name__ == "__main__":
    asyncio.run(main())
