#include "threadpool.hpp"

// Destructor to clean up the ThreadPool
ThreadPool::~ThreadPool()
{
    /* Stop the thread pool and notify all threads to finish remaining tasks. */
    {
        std::unique_lock<std::mutex> lock(mutex_);
        stop = true;
    }
    conditional_variable_.notify_all();
    for (auto &worker : workers)
        worker.join();
}

// Constructor to initialize the ThreadPool with a specified number of threads
ThreadPool::ThreadPool(size_t number_of_threads) : stop(false)
{
    for (size_t i = 0; i < number_of_threads; ++i)
    {
        // Create and start worker threads
        std::thread worker([this]() {
            while (true)
            {
                std::function<void()> task;
                /* Pop a task from the queue and execute it. */
                {
                    std::unique_lock lock(mutex_);
                    conditional_variable_.wait(lock, [this]() { return stop || !tasks.empty(); });
                    if (stop && tasks.empty())
                        return;
                    /* Even if `stop` is set to 1, keep executing tasks until the queue becomes empty. */
                    task = std::move(tasks.front());
                    tasks.pop();
                }
                task();
            }
        });
        workers.emplace_back(std::move(worker));
    }
}

void ThreadPool::enqueue(std::function<void()> task) 
{
    {
        std::unique_lock lock(mutex_);
        if (stop) {
            throw std::runtime_error("The thread pool has been stopped.");
        }
        tasks.emplace(task);
    }
    conditional_variable_.notify_one();
}
