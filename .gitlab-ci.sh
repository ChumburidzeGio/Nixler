#!/bin/bash

# Update packages and install composer and PHP dependencies.
apt-get update -yqq

# Compile PHP, include these extensions.
docker-php-ext-install gd