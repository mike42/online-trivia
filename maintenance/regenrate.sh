#!/bin/bash
# Save the config
echo "Saving"..

# Regenerate
echo "Generating.."
rm -Rf ../trivia
cd ../../php-mysql-model-generator
rm -Rf trivia && ./generate.php trivia < ../online-trivia/maintenance/database/trivia.sql
mv trivia ../online-trivia/
cd ../online-trivia

# Replace config
echo "Replacing.."

