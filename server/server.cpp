#include <iostream>
#include "server.hpp"

// Constructor
Server::Server(net::io_context& io_context, short number_of_threads, short port)
    : port_(port),
      io_context_(io_context),
      number_of_threads_(number_of_threads), 
      thread_pool_(number_of_threads)
    {

    }

// Main server loop
void Server::run()
{
    // Initialize the acceptor
    tcp::acceptor acceptor_(io_context_, tcp::endpoint(tcp::v4(), port_));
    while (true) 
     {

        // Create a socket for the connection
        std::shared_ptr<tcp::socket> socket_ = std::make_shared<tcp::socket>(io_context_);
    
        // Accept incoming connection
        acceptor_.accept(*socket_);

        std::cout<<"\nConnection accepted\n";


        thread_pool_.enqueue(
            [this, socket_]
            {
                // Handle the request
                handle_request(*socket_);

                // Close the socket
                boost::system::error_code ec;
                socket_->shutdown(tcp::socket::shutdown_send, ec);
                socket_->close(ec);
                if (ec)
                {
                    std::cerr << "Error closing socket: " << ec.message() << std::endl;
                }

            }
        );
        
    }
     
}

// Handle incoming request
void Server::handle_request(tcp::socket& socket_) {
    // Read the request from the socket
    beast::flat_buffer buffer;
    http::request<http::dynamic_body> request;
    http::read(socket_, buffer, request);

    // Prepare the response
    http::response<http::string_body> response;

    // Check the request method and handle accordingly
    if (request.method() == http::verb::get) 
    {
        handle_get_request(request, response);
    } 

    // Write the response to the socket
    http::write(socket_, response);
}

// Handle GET request
void Server::handle_get_request(const http::request<http::dynamic_body>& request, http::response<http::string_body>& response) 
{
    // Create a JSON response as a string
    std::string json_response = R"({"message": "Hello from C++ microservice."})";

    // Set response headers
    response.version(request.version());
    response.result(http::status::ok);
    response.set(http::field::server, "FileTokenizer");
    response.set(http::field::content_type, "application/json");
    response.body() = json_response;
    response.prepare_payload();
}
