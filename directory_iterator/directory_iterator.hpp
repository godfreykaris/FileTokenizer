#pragma once

#include <vector>
#include <string>

class DirectoryIterator {
public:
    DirectoryIterator() = default;

    std::vector<std::string> get_files(const std::string& directory_path,const std::vector<std::string>& extensions) const;
    std::vector<std::string> get_directories(const std::string& directory_path) const;

};