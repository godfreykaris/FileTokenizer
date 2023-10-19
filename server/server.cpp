#include <iostream>

#include "server.hpp"

Server::Server(net::io_context& io_context, short port)
    : acceptor_(io_context, tcp::endpoint(tcp::v4(), port)), socket_(io_context) {
    }   

void Server::run()
{
   while (true) 
   {
        acceptor_.accept(socket_);

        // Handle the request
        handle_request();

        // Close the socket
        boost::system::error_code ec;
        socket_.shutdown(tcp::socket::shutdown_send, ec);
        socket_.close(ec);
        if (ec)
        {
            std::cerr << "Error closing socket: " << ec.message() << std::endl;
        }
    }
}

void Server::handle_request() {
    beast::flat_buffer buffer;
    http::request<http::dynamic_body> request;
    http::read(socket_, buffer, request);

    http::response<http::string_body> response;

    if (request.method() == http::verb::get) 
    {
        handle_get_request(request, response);
    } 

    http::write(socket_, response);
}

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

Server::~Server()
{
    acceptor_.close();
}
