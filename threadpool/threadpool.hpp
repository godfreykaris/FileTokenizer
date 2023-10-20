#pragma once

#include <functional>
#include <future>
#include <iostream>
#include <queue>
#include <thread>
#include <vector>

class ThreadPool
{
  public:
    // Prevent copying and moving the ThreadPool
    ThreadPool(const ThreadPool &) = delete;
    ThreadPool(ThreadPool &&) = delete;
    ThreadPool &operator=(const ThreadPool &) = delete;
    ThreadPool &operator=(ThreadPool &&) = delete;

    // Constructor: Initialize the ThreadPool with a given number of threads
    ThreadPool(size_t number_of_threads);

    // Destructor: Clean up resources when the ThreadPool is destroyed
    virtual ~ThreadPool();

    // Function to enqueue a task for execution
    void enqueue(std::function<void()> task);

    bool stop;                   // Flag to signal the threads to stop

  private:
    // A collection of worker threads
    std::vector<std::thread> workers;

    // A queue of functions (tasks) to be executed by the threads
    std::queue<std::function<void()>> tasks;


    // Synchronization resources for managing concurrent access to the task queue
    std::mutex mutex_;              // Mutex for protecting shared resources
    std::condition_variable conditional_variable_;  // Condition variable for coordinating threads
};

