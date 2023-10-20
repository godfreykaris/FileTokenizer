#pragma once

#include <vector>
#include <string>
#include <memory>

#include "tokenizer/tokenizer.hpp"


class DirectoryIterator 
{
    public:
        DirectoryIterator();
        ~DirectoryIterator() = default;

        void process_files(const std::string& directory_path,const std::vector<std::string>& extensions) const;
        void tokenize_and_write_to_csv(const std::string& file_path) const;

        std::shared_ptr<Tokenizer> tokenizer_;
};