"""
LRU Cache Implementation
Time Complexity: O(1) for both get and put
Space Complexity: O(capacity)

Data Structures Used:
- Hash Map (dict): Provides O(1) key lookup to find nodes
- Doubly Linked List: Maintains usage order with O(1) move/delete operations
"""

class Node:
    """Doubly linked list node storing key-value pairs."""
    __slots__ = ('key', 'value', 'prev', 'next')
    
    def __init__(self, key: int, value: int):
        self.key = key
        self.value = value
        self.prev = None
        self.next = None


class LRUCache:
    """
    LRU Cache using hash map + doubly linked list.
    
    The hash map stores key -> node references for O(1) access.
    The doubly linked list maintains order: head = most recent, tail = least recent.
    """
    
    def __init__(self, capacity: int):
        self.capacity = capacity
        self.cache = {}  # key -> Node
        
        # Dummy head and tail for easier edge case handling
        self.head = Node(0, 0)
        self.tail = Node(0, 0)
        self.head.next = self.tail
        self.tail.prev = self.head
    
    def _remove(self, node: Node) -> None:
        """Remove node from linked list. O(1)"""
        prev_node = node.prev
        next_node = node.next
        prev_node.next = next_node
        next_node.prev = prev_node
    
    def _add_to_front(self, node: Node) -> None:
        """Add node right after head (most recent). O(1)"""
        node.next = self.head.next
        node.prev = self.head
        self.head.next.prev = node
        self.head.next = node
    
    def _move_to_front(self, node: Node) -> None:
        """Move existing node to front. O(1)"""
        self._remove(node)
        self._add_to_front(node)
    
    def _pop_tail(self) -> Node:
        """Remove and return the least recently used node. O(1)"""
        lru = self.tail.prev
        self._remove(lru)
        return lru
    
    def get(self, key: int) -> int:
        """
        Get value by key. Moves item to front (most recent).
        Time: O(1)
        """
        if key not in self.cache:
            return -1
        
        node = self.cache[key]
        self._move_to_front(node)
        return node.value
    
    def put(self, key: int, value: int) -> None:
        """
        Insert or update key-value pair. Moves item to front.
        Evicts LRU if capacity exceeded.
        Time: O(1)
        """
        if key in self.cache:
            # Update existing
            node = self.cache[key]
            node.value = value
            self._move_to_front(node)
        else:
            # Create new node
            new_node = Node(key, value)
            self.cache[key] = new_node
            self._add_to_front(new_node)
            
            # Evict if over capacity
            if len(self.cache) > self.capacity:
                lru = self._pop_tail()
                del self.cache[lru.key]


# Example usage and test
if __name__ == "__main__":
    cache = LRUCache(2)
    
    cache.put(1, 1)          # Cache: {1=1}
    cache.put(2, 2)          # Cache: {1=1, 2=2}
    print(cache.get(1))      # Returns 1, Cache: {2=2, 1=1}
    cache.put(3, 3)          # Evicts 2, Cache: {1=1, 3=3}
    print(cache.get(2))      # Returns -1 (not found)
    cache.put(4, 4)          # Evicts 1, Cache: {3=3, 4=4}
    print(cache.get(1))      # Returns -1 (not found)
    print(cache.get(3))      # Returns 3, Cache: {4=4, 3=3}
    print(cache.get(4))      # Returns 4, Cache: {3=3, 4=4}
