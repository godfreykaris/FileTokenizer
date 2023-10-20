
#include <filesystem>
#include <iostream>
#include <algorithm>

#include "directory_iterator.hpp"

namespace fs = std::filesystem;

DirectoryIterator::DirectoryIterator()
    :tokenizer_(std::make_shared<Tokenizer>())
{
    // Constructor
}

void DirectoryIterator::process_files(const std::string& directory_path, const std::vector<std::string>& extensions) const 
{
    if (!std::filesystem::exists(directory_path))
    {
        throw std::runtime_error("Directory does not exist: " + directory_path);
    }

    try 
    {
        for (const auto& entry : fs::directory_iterator(directory_path)) 
        {
            if (fs::is_regular_file(entry.path())) 
            {
                std::string extension = entry.path().extension().string();
                if (std::find(extensions.begin(), extensions.end(), extension) != extensions.end())
                {
                    tokenize_and_write_to_csv(entry.path().filename().string());
                }
            }
            else if (fs::is_directory(entry.path())) 
            {
                process_files(entry.path().string(), extensions);
            }
        }
    } 
    catch (const std::filesystem::filesystem_error& ex) 
    {
        std::cerr << "Filesystem error: " << ex.what() << std::endl;
    }

}

void DirectoryIterator::tokenize_and_write_to_csv(const std::string& file_path) const
{
    tokenizer_->tokenize(file_path);
}