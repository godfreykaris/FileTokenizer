#include "tokenizer.hpp"

#include <regex>
#include <fstream>
#include <filesystem>
#include <iostream>
#include <boost/algorithm/string.hpp>

void Tokenizer::tokenize(const std::string& file_path) 
{
    std::vector<TokenInfo> tokens;

    std::ifstream file(file_path);
    std::string line;
    int line_number = 1;

    while (std::getline(file, line)) 
    {
        if (is_sql_code(line)) 
        {
            std::string token_string = line;
            boost::trim(token_string);
            
            TokenInfo token_info;
            token_info.token = token_string;
            token_info.file_path = file_path;
            token_info.line_number = line_number;
            tokens.push_back(token_info);
        }
        line_number++;
    }

    file.close();

    if(tokens.size())
        write_to_csv(tokens, file_path);

}

bool Tokenizer::is_sql_code(const std::string& line) 
{
    // Define a single regex pattern to match SQL statements
    std::regex sql_regex(R"(\b(SELECT|INSERT|UPDATE|DELETE|CREATE|ALTER|DROP)\b\s+.*\b(FROM|INTO|SET|TABLE)\b)", std::regex_constants::icase);

    // Check if the line contains any SQL statement
    return std::regex_search(line, sql_regex);
}

void Tokenizer::write_to_csv(const std::vector<TokenInfo>& token_info, const std::string& file_path) 
{
    // Extract the directory path and file name from the given file path
    std::filesystem::path path = std::filesystem::path(file_path);
    path.replace_extension(".csv");
        
    std::ofstream file(path);

    if (!file.is_open()) 
    {
        std::cout << "Failed to open file: " << path << std::endl;
        return;
    }

    // Write the header row
    file << "Token~FilePath~LineNumber" << std::endl;

    // Write token information
    for (const auto& info : token_info) 
    {
        file << std::quoted(info.token) << "~" <<  std::quoted(info.file_path) << "~" << info.line_number << std::endl;
    }

    file.close();
}