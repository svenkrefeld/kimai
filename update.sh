#!/usr/bin/env bash

# Install Composer dependencies with optimized autoloader and no interaction
composer install --optimize-autoloader -n

# Run the Kimai update command
bin/console kimai:update