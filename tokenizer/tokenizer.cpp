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
    // std::string code_block = "";
    // std::string selected_block = "";
    // size_t pos;
    int line_number = 1;

    // // Flags to track state and counting of added lines
    // bool adding_lines_after = false;
    // int added_lines = 0;

    TokenInfo token_info;

    while (std::getline(file, line)) {
        boost::trim(line);  // Trim leading and trailing spaces from the line.

        // if (!adding_lines_after) 
        // {
        //    if(line_number > 5)
        //    {
        //          // If not in the state of adding lines after SQL code.
        //         pos = code_block.find_first_of("\n");
        //         if (pos != std::string::npos) 
        //         {
        //             // Remove the earliest line.
        //             code_block = code_block.substr(pos + 1);
        //         }
        //    }

        //     // Add the current line to the code block.
        //     code_block.append(line + "\n");

        //     if (is_sql_code(line)) {
        //         // SQL code detected, switch to the state of adding lines after SQL code.
        //         adding_lines_after = true;
        //         token_info.file_path = file_path;
        //         token_info.line_number = line_number;
        //     }
        // } 
        // else 
        // {
        //     if (added_lines <= 5) 
        //     {
        //         // Add lines after SQL code.
        //         code_block.append(line + "\n");
        //         added_lines++;
        //     } else 
        //     {
        //         // Reached the maximum of 5 lines, create a token and reset counters.
        //         selected_block = code_block;
        //         std::replace(selected_block.begin(), selected_block.end(), '\n', ' ');
        //         token_info.token = selected_block;
        //         tokens.push_back(token_info);
        //         added_lines = 0;
        //         adding_lines_after = false;
        //     }
        // }

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

    path = std::filesystem::path(std::string("/home/godfreykaris/Documents/CVI/dataset/") + path.filename().string());

    std::ofstream file(path);

    if (!file.is_open()) 
    {
        std::cout << "Failed to open file: " << path << std::endl;
        return;
    }

    // Write the header row
    //file << "Token~FilePath~LineNumber" << std::endl;
    file << "Code~VulnerabilityStatus" << std::endl;

    
    // // Write token information
    // for (const auto& info : token_info) 
    // {
    //     //file << std::quoted(info.token) << "~" <<  std::quoted(info.file_path) << "~" << info.line_number << std::endl;
    //     file << std::quoted(info.token) << "~" <<  0 << std::endl;

    // }

    // Write token information
    bool alternate = false; // Start with 0
    for (const auto& info : token_info) 
    {
        int value = alternate ? 1 : 0;
        file << std::quoted(info.token) << "~" << value << std::endl;
        alternate = !alternate; // Toggle the value for the next iteration
    }

    file.close();
}