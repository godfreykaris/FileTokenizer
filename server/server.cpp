#include <iostream>
#include<nlohmann/json.hpp>
#include "server.hpp"

using json = nlohmann::json;

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
    if(request.method() == http::verb::get) 
    {
        handle_get_request(request, response);
    } 
    else if(request.method() == http::verb::post) 
    {
        handle_post_request(request, response);
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

// Handle POST request
void Server::handle_post_request(const http::request<http::dynamic_body>& request, http::response<http::string_body>& response) 
{
    // Extract the request body as JSON
    json request_json;
    try 
    {
        request_json = json::parse(boost::beast::buffers_to_string(request.body().data()));
    } 
    catch (const std::exception& e) 
    {
        // Handle JSON parsing error
        response.result(http::status::bad_request);
        response.set(http::field::content_type, "text/plain");
        response.body() = "Invalid JSON in request body";
        response.prepare_payload();
        return;
    }

    // Process the request JSON and generate a response
    json response_json = process_post_request(request_json);

    // Set response headers
    response.version(request.version());
    response.result(http::status::ok);
    response.set(http::field::server, "FileTokenizer");
    response.set(http::field::content_type, "application/json");
    response.body() = response_json.dump();
    response.prepare_payload();
}

// Function to process the POST request JSON and generate a response JSON
json Server::process_post_request(const json& request_json)
{
    // Process the request JSON here and generate a response JSON
    json response_json;
    // ... process the request JSON and populate the response JSON ...
    response_json["message"] = "Response to POST request: ";

    return response_json;
}
