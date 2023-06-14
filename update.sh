#!/usr/bin/env bash

# Fetch the latest tags from the remote repository
git fetch --tags

# Run the command and store the result in a variable
latest_tag=$(git tag -l | sort -V | tail -1)

# Display the latest tag
echo "Update to latest tag: $latest_tag"

# Checkout to the "release" branch based on the latest tag
git checkout -b release-$latest_tag $latest_tag

# Install Composer dependencies with optimized autoloader and no interaction
composer install --optimize-autoloader -n

# Run the Kimai update command
bin/console kimai:update

# Store the latest_tag value in the "VERSION" file
echo "$latest_tag" > VERSION

# Stage the updated VERSION file for commit
git add VERSION
