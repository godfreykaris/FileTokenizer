#pragma once

#include <string>
#include <vector>

struct TokenInfo 
{
    std::string token;
    std::string file_path;
    int line_number;
};


class Tokenizer 
{
    public:
        Tokenizer() = default;
        ~Tokenizer() = default;

        std::vector<TokenInfo> tokenize(const std::string& file_path);

    private:
        bool is_sql_code(const std::string& line);
        void write_to_csv(const std::vector<TokenInfo>& token_info, const std::string& file_path);
};