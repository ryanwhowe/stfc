#!/bin/sh

############
# DB Check #
############

# Wait for DB server to be up.
echo "Checking if database server is up..."
bash /tmp/scripts/wait-for-it.sh ${DATABASE_HOST}:${DATABASE_PORT} --timeout=180

# Get the exit code.
db_up=$?

# Check for DB connect success
if [ $db_up -eq 0 ];
then
    echo "Found database server, proceeding."
else
    echo "Database server could not be reached, exiting."
    exit $db_up
fi

#########################
# Start php Cli process #
#########################
php bin/console ${SYMFONY_COMMAND}
