#include <memory>
#include <iostream>
#include <boost/asio.hpp>

#include "server/server.hpp" 


int main() 
{
    boost::asio::io_context io_context;

    try 
    {
        // Create a Server instance, passing the io_context and the port you want to listen on (e.g., 8080).
        // Use a smart pointer
        std::unique_ptr<Server> server = std::make_unique<Server>(io_context, 8080);

        // Start the server
        server->start();

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
