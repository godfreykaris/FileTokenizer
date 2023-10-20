#pragma once

#include <boost/beast.hpp>
#include <boost/asio.hpp>
#include <nlohmann/json.hpp>

#include "../threadpool/threadpool.hpp"

// Namespace aliases for convenience
namespace beast = boost::beast;
namespace http = beast::http;
namespace net = boost::asio;
using tcp = boost::asio::ip::tcp;
using json = nlohmann::json;

// Declaration of the Server class
class Server 
{
    public:
        // Constructor: Initializes the server with the given io_context, number of threads, and port
        Server(net::io_context& io_context, short number_of_threads, short port);
    
        // Destructor: Default destructor
        ~Server() = default;
    
        // Runs the server, accepting incoming connections and processing requests
        void run();
    
    private:
        // Accepts an incoming connection
        void accept();
    
        // Handles an incoming request
        void handle_request(tcp::socket& socket_);
    
        // Handles an HTTP GET request
        void handle_get_request(const http::request<http::dynamic_body>& request, http::response<http::string_body>& response);
    
        // Handles an HTTP POST request
        void handle_post_request(const http::request<http::dynamic_body>& request, http::response<http::string_body>& response);
    
        // Processes a POST request and returns a JSON response
        json process_post_request(const json& request_json);
    
        // Member variables
        short port_;                    // The port on which the server listens
        net::io_context& io_context_;   // The io_context used for asynchronous operations
        short number_of_threads_ = 1;   // The number of threads in the server's thread pool
        ThreadPool thread_pool_;        // The thread pool used for handling requests
};