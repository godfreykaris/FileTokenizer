// server.hpp
#pragma once

#include <boost/beast.hpp>
#include <boost/asio.hpp>

namespace beast = boost::beast;
namespace http = beast::http;
namespace net = boost::asio;
using tcp = boost::asio::ip::tcp;

class Server 
{
    public:
        Server(net::io_context& io_context, short number_of_threads, short port);
        ~Server();
        void run();

    private:
        void accept();
        void handle_request(tcp::socket& socket_);
        void handle_get_request(const http::request<http::dynamic_body>& request, http::response<http::string_body>& response);

        tcp::acceptor acceptor_;
        net::io_context& io_context_;
        short number_of_threads_ = 1;

};
