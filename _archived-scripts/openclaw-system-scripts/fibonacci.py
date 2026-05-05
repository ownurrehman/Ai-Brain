def fibonacci_sequence(n):
    """Generate the first n numbers in the Fibonacci sequence."""
    if n <= 0:
        return []
    elif n == 1:
        return [0]
    elif n == 2:
        return [0, 1]
    
    # Initialize the sequence with the first two numbers
    fib_seq = [0, 1]
    
    # Generate the rest of the sequence
    for i in range(2, n):
        # Each number is the sum of the two preceding numbers
        next_num = fib_seq[i-1] + fib_seq[i-2]
        fib_seq.append(next_num)
    
    return fib_seq

# Calculate and print the first 10 Fibonacci numbers
if __name__ == "__main__":
    first_10_fib = fibonacci_sequence(10)
    print("First 10 Fibonacci numbers:")
    print(first_10_fib)