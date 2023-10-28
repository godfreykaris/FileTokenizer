
#include <filesystem>
#include <iostream>
#include <algorithm>
#include <fstream>


#include "directory_iterator.hpp"

namespace fs = std::filesystem;

DirectoryIterator::DirectoryIterator()
    :tokenizer_(std::make_shared<Tokenizer>())
{
    // Constructor
}

std::vector<std::string> DirectoryIterator::get_files(const std::string& directory_path, const std::vector<std::string>& extensions) const 
{
    std::vector<std::string> files;


    if (!std::filesystem::exists(directory_path))
    {
        throw std::runtime_error("Directory does not exist: " + directory_path);
    }

    try 
    {
        std::filesystem::path path = std::filesystem::path(std::string("/home/godfreykaris/Documents/CVI/AI-CVI/combined_dataset.csv"));

        std::ofstream file1(path);

        if (!file1.is_open()) 
        {
            std::cout << "Failed to open file: " << path << std::endl;
            return files;
        }

        for (const auto& entry : fs::directory_iterator(directory_path)) 
        {
            if (fs::is_regular_file(entry.path())) 
            {
                // std::string extension = entry.path().extension().string();
                // if (std::find(extensions.begin(), extensions.end(), extension) != extensions.end())
                // {
                //     files.push_back(entry.path());
                // }

                std::ifstream file(entry.path().string());
                std::string line;

        
                int added_lines = 0;

                while (std::getline(file, line)) 
                {
                    if(added_lines)
                        file1 << line << std::endl;
                    
                    added_lines++;
                }
            }
            else if (fs::is_directory(entry.path())) 
            {
                std::vector<std::string> sub_directory_files = get_files(entry.path().string(), extensions);
                files.insert(files.end(), sub_directory_files.begin(), sub_directory_files.end());
            }
        }
    } 
    catch (const std::filesystem::filesystem_error& ex) 
    {
        std::cerr << "Filesystem error: " << ex.what() << std::endl;
    }

    return files;
}

std::vector<TokenInfo> DirectoryIterator::tokenize_and_write_to_csv(const std::string& file_path) const
{
    return tokenizer_->tokenize(file_path);
}