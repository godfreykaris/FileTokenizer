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
        Server(net::io_context& io_context, short port);
        ~Server();
        void run();
        void start();

        net::io_context io_context;
        tcp::acceptor acceptor_;
        tcp::socket socket_;

    private:
        void accept();
        void handle_request();
        void handle_get_request(const http::request<http::dynamic_body>& request, http::response<http::string_body>& response);

};
