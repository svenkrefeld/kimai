#!/usr/bin/env bash

# Check if there are uncommitted changes
if [[ -n $(git status --porcelain) ]]; then
    echo "Error: There are uncommitted changes in the Git repository. Aborting script."
    exit 1
fi

# Continue with the rest of your script if there are no uncommitted changes
echo "No uncommitted changes found. Continuing with the script..."

# Fetch the latest tags from the remote repository
git fetch upstream --tags

# Run the command and store the result in a variable
latest_tag=$(git tag -l | sort -V | tail -1)

# Display the latest tag
echo "Update to latest tag: $latest_tag"

# Checkout to the "release" branch based on the latest tag
git checkout -b release-$latest_tag $latest_tag

# Merge the main branch on the latest release
git merge origin main

# Store the latest_tag value in the "VERSION" file
echo "$latest_tag" > VERSION

# Stage the updated VERSION file for commit
git add VERSION

# Check if there are merge conflicts
if git status | grep -q "both modified"; then
    echo "Error: There are merge conflicts in the Git repository. Aborting script."
    exit 1
fi

# Continue with the rest of your script if there are no merge conflicts
echo "No merge conflicts found. Continuing with the script..."

./update.sh