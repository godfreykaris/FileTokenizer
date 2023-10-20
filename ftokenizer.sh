#!/bin/bash

# Compile the C++ program
g++ -o app \
    app.cpp \
    threadpool/threadpool.cpp \
    directory_iterator/directory_iterator.cpp \
    tokenizer/tokenizer.cpp \
    server/server.cpp \
    -I server \
    -I threadpool \
    -I directory_iterator \
    -I tokenizer \
    -lboost_system \
    -lboost_thread \
    -lssl \
    -lcrypto \
    -pthread

# Check if the compilation was successful
if [ $? -eq 0 ]; then
    echo -e "Compilation succeeded."

    # Run the compiled program
    echo -e "Running the program...\n"
    ./app
else
    echo -e "Compilation failed.\n"
fi