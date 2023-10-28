#include <iostream>
#include<nlohmann/json.hpp>
#include "server.hpp"

#include "../directory_iterator/directory_iterator.hpp"

using json = nlohmann::json;

// Constructor
Server::Server(net::io_context& io_context, short number_of_threads, short port, const std::string& ai_microservice_url)
    : port_(port),
      io_context_(io_context),
      number_of_threads_(number_of_threads), 
      thread_pool_(number_of_threads),
      ai_microservice_url_(ai_microservice_url)
    {
        curl_ = curl_easy_init();
        if (curl_) 
        {
            curl_easy_setopt(curl_, CURLOPT_POST, 1L);
        }
    }

Server::~Server()    
{
   if (curl_) 
   {
        curl_easy_cleanup(curl_);
   }
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
                handle_request(socket_);

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
void Server::handle_request(std::shared_ptr<tcp::socket> socket_) {
    // Read the request from the socket
    beast::flat_buffer buffer;
    http::request<http::dynamic_body> request;
    http::read(*socket_, buffer, request);

    // Prepare the response
    http::response<http::string_body> response;

    // Check the request method and handle accordingly
    if(request.method() == http::verb::get) 
    {
        handle_get_request(request, response);
    } 
    else if(request.method() == http::verb::post) 
    {
        handle_post_request(socket_, request, response);
    } 

    // Write the response to the socket
    http::write(*socket_, response);
}

void Server::set_common_response_headers(
    const http::request<http::dynamic_body>& request, 
    http::response<http::string_body>& response, 
    const json& response_json,
    http::status status
    ) 
{
    response.version(request.version());
    response.result(status);
    response.set(http::field::server, "FileTokenizer");
    response.set(http::field::content_type, "application/json");
    response.body() = response_json.dump();
    response.prepare_payload();
}


// Handle GET request
void Server::handle_get_request(const http::request<http::dynamic_body>& request, http::response<http::string_body>& response) 
{
    // Create a JSON response as a string
    json json_response;
    json_response["message"] = "Hello from C++ microservice.";

    // Set response headers
    set_common_response_headers(request, response, json_response, http::status::ok);
}

// Handle POST request
void Server::handle_post_request(std::shared_ptr<tcp::socket> socket_, const http::request<http::dynamic_body>& request, http::response<http::string_body>& response) 
{
    // Extract the request body as JSON
    json request_json;
    try 
    {
        request_json = json::parse(boost::beast::buffers_to_string(request.body().data()));
    } 
    catch (const std::exception& e) 
    {
        // Create a JSON response as a string
        json json_response;
        json_response["message"] = "Invalid JSON in request body";

        // Set response headers
        set_common_response_headers(request, response, json_response, http::status::bad_request);

        return;
    }

    // Process the request JSON and generate a response
    json response_json = process_post_request(request_json);

    // Set response headers
    set_common_response_headers(request, response, response_json, http::status::ok);

}

// Function to process the POST request JSON and generate a response JSON
json Server::process_post_request(const json& request_json)
{
    // Process the request JSON here and generate a response JSON
    json response_json;

    // Check if the "directory" and "file_extensions" keys exists in the JSON object
    if (request_json.find("directory") != request_json.end() &&
        request_json.find("file_extensions") != request_json.end())
    {

        std::string directory = request_json.at("directory");
        
        std::shared_ptr<DirectoryIterator> directory_iterator = std::make_shared<DirectoryIterator>();

        std::vector<std::string> extensions = request_json.at("file_extensions"); 

        std::vector<std::string> file_paths;
        try 
        {        
            file_paths = directory_iterator->get_files(directory, extensions);

            for (const std::string& file_path : file_paths) 
            {
                thread_pool_.enqueue(
                [this, file_path = file_path, directory_iterator]
                {

                    std::vector<TokenInfo> created_tokens = directory_iterator->tokenize_and_write_to_csv(file_path);
                    
                    if(created_tokens.size() > 0)
                    {
                        json processing_complete_response;
                        processing_complete_response["message"] = "Finished tokenizing file: " + file_path;

                        // Get the output file
                        std::filesystem::path path = std::filesystem::path(file_path);
                        path.replace_extension(".csv");
                
                        processing_complete_response["output"] = path.string();

                        //  bool success = send_post_request(ai_microservice_url_, processing_complete_response);

                        // if (!success)
                        // {
                        //     std::cerr << "Failed to send POST request." << std::endl;
                        // }
                    }
                    
                }
                );
            }

            response_json["message"] = "Working...";

        } 
        catch (const std::runtime_error& ex) 
        {
            std::cerr << "Error: " << ex.what() << std::endl;
            response_json["message"] = "An error occurred: " + std::string(ex.what());
        }
    }
    else
    {
        response_json["message"] = "Directory path or file extensions not provided in the request.";
    }

    return response_json;
}


bool Server::send_post_request(const std::string& target_url, const json& post_data) 
{
    const int max_attempts = 10;
    CURLcode res = CURLE_OK;

    for (int attempt = 1; attempt <= max_attempts; ++attempt) 
    {
        if (curl_) 
        {
            // Set the target URL
            curl_easy_setopt(curl_, CURLOPT_URL, target_url.c_str());

            // Set the request data
            const std::string json_data = post_data.dump();
            curl_easy_setopt(curl_, CURLOPT_POSTFIELDS, json_data.c_str());

            // Set the content type header
            struct curl_slist* headers = NULL;
            headers = curl_slist_append(headers, "Content-Type: application/json");
            curl_easy_setopt(curl_, CURLOPT_HTTPHEADER, headers);

            // Perform the request
            res = curl_easy_perform(curl_);

            curl_slist_free_all(headers);

            if (res == CURLE_OK) 
            {
                return true; // Request successful, no need for further attempts
            } 
            else 
            {
                // Print error message and retry after a delay
                std::cerr << "Attempt " << attempt << " failed: " << curl_easy_strerror(res) <<"\nRetrying..." << std::endl;
                std::this_thread::sleep_for(std::chrono::seconds(1)); // Delay before the next attempt
            }
        }
    }

    // All attempts failed, print an error message and return false
    std::cerr << "All " << max_attempts << " attempts failed. Last error: " << curl_easy_strerror(res) << std::endl;
    return false;
}