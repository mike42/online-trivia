#!/bin/bash
# Save the config
echo "Saving"..

# Regenerate
echo "Generating.."
cd ../../php-mysql-model-generator
rm -Rf dl && ./generate.php trivia < ../smartphone-trivia/maintenance/database/trivia.sql
mv trivia ../smartphone-trivia/
cd ../smartphone-trivia

# Replace config
echo "Replacing.."

