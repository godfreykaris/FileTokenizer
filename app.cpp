#include <memory>
#include <iostream>
#include <boost/asio.hpp>

#include "server/server.hpp" 


int main() 
{
    // Number of threads that can be executed simultaneously by the hardware
    int number_of_threads = std::thread::hardware_concurrency();

    // Central component used for managing I/O operations
    boost::asio::io_context io_context;

    std::string ai_url = "http://127.0.0.1:8000/api";

    try 
    {
        // Create a Server instance, passing the io_context and the port you want to listen on (e.g., 8080).
        // Use a smart pointer
        std::unique_ptr<Server> server = std::make_unique<Server>(io_context, number_of_threads, 8080, ai_url);

        // Run the server
        server->run();

        // Run the Boost.Asio io_context to handle asynchronous operations
        io_context.run();
    } 
    catch (const std::exception& ex) 
    {
        std::cerr << "Exception: " << ex.what() << std::endl;
        return 1;
    }

    return 0;
}
