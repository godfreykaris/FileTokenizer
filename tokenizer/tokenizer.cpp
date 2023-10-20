#include "tokenizer.hpp"

#include <regex>
#include <fstream>
#include <filesystem>
#include <iostream>

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
            TokenInfo token_info;
            token_info.token = line;
            token_info.file_path = file_path;
            token_info.line_number = line_number;
            tokens.push_back(token_info);
        }
        line_number++;
    }

    file.close();

    write_to_csv(tokens, file_path);
    
}

bool Tokenizer::is_sql_code(const std::string& line) 
{
    // Modify this function to check for SQL code patterns
    std::regex sql_regex(".*?(SELECT|INSERT|UPDATE|DELETE|CREATE|ALTER|DROP).*?", std::regex_constants::icase);
    return std::regex_match(line, sql_regex);
}

void Tokenizer::write_to_csv(const std::vector<TokenInfo>& token_info, const std::string& file_path) 
{
    // Extract the directory path and file name from the given file path
    std::filesystem::path path = std::filesystem::path(file_path);
    std::string directory = path.parent_path().string();
    std::string file_name = path.stem().string();
    
    // Create the file path for the CSV file in the same directory with the same name
    std::string csv_file_path = directory + "/" + file_name + ".csv";
    
    std::ofstream file(csv_file_path);

    if (!file.is_open()) 
    {
        std::cout << "Failed to open file: " << csv_file_path << std::endl;
        return;
    }

    // Write the header row
    file << "Token,FilePath,LineNumber" << std::endl;

    // Write token information
    for (const auto& info : token_info) 
    {
        file << info.token << "," << info.file_path << "," << info.line_number << std::endl;
    }

    file.close();
}