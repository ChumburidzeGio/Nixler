#!/bin/bash

# Install dependencies only for Docker.
[[ ! -e /.dockerinit ]] && exit 0
set -xe

# Update packages and install composer and PHP dependencies.
apt-get update -yqq

# Compile PHP, include these extensions.
docker-php-ext-install gd