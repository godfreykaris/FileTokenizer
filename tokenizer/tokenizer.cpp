#include "tokenizer.hpp"

#include <regex>
#include <fstream>
#include <filesystem>
#include <iostream>
#include <boost/algorithm/string.hpp>

std::vector<TokenInfo> Tokenizer::tokenize(const std::string& file_path) {
    std::vector<TokenInfo> tokens;

    std::ifstream file(file_path);
    std::string line;
    int line_number = 1;

    TokenInfo token_info;

    while (std::getline(file, line)) {
        boost::trim(line);  // Trim leading and trailing spaces from the line.

        if (is_sql_code(line)) 
        {
            token_info.file_path = file_path;
            token_info.line_number = line_number;
            token_info.token = line;
            tokens.push_back(token_info);
        }
        

        line_number++;
    }

    file.close();

    if (!tokens.empty())
        write_to_csv(tokens, file_path);

    return tokens;
}


bool Tokenizer::is_sql_code(const std::string& line) 
{
    if (line.empty()) 
    {
        return false;
    }

    // Check if the line contains any SQL statement
    try 
    {
        // Define a single regex pattern to match SQL statements
        std::regex sql_regex(R"(\b(UPDATE|INSERT|SELECT|DELETE)\b.*?\b(SET|VALUES|WHERE)\b.*?(\w+)\s*=\s*('[^']*'|\\?)\s*)", std::regex_constants::icase);
        bool match = std::regex_search(line.substr(0, 200), sql_regex);

        return match;
    } 
    catch (const std::regex_error& e) 
    {
        std::cerr << "Regex error: " << e.what() << std::endl;
        return true; 
    }
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
    file << "Line~Code~File" << std::endl;
    // file << "Code~VulnerabilityStatus" << std::endl;

    
    // Write token information
    for (const auto& info : token_info) 
    {
        file << info.line_number << "~" << std::quoted(info.token) << "~" <<  std::quoted(info.file_path) << std::endl;
        // file << std::quoted(info.token) << "~" <<  0 << std::endl;

    }

    file.close();
}