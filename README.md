# stfc

**WARNING:** if you are deploying on a Windows machine avoid cloning the repo into a subdirectory of your Documents
folder.  Windows does some very heavy simlinking to that directory, and it usually confuses docker.  Clone the repo to a
simple directory, like `c:\projects\stfc`

## About
This is an attempt to collect data about star trek fleet command through their public api.  The end goal of which is to
create tools to help with the game play mechanics.


## Project Setup

### setup environment .env file
copy the included `.env` file to a `.env.local` this will contain the local environment settings that will be used by
symfony.  The `DATABASE_URL` and `SCOPLEY_VERSION` will need to have values provided for them.  For local development in
docker the correct `DATABASE_URL` is `mysql://root:root@db:3306/stfc?serverVersion=5.7`.  Please get the 
`SCOPLEY_VERSION` from a fellow developer to use for your local environment.  For running tests locally you will also
need to setup the `.env.test` to a `.env.test.local` file.  The only value that the `.env.test.local` file will need is
the database connection.  This connection should point to a dev database instance, for local docker development the 
appropriate `DATABASE_URL` is `mysql://root:root@db:3306/stfc_test?serverVersion=5.7` which will point to a `stfc_test`
database for running test cases.

**NOTE:** these files are excluded from the repository so these local secrets are not at risk for being published to the
repo.

From the project root
```shell
cp .env .env.local
cp .env.test .env.test.local
```

### setup docker environment
Assuming that you have already installed docker on your local machine.  You will need the full path to the solution 
folder.  Once the solution is cloned you will need to copy the `devops/dev/docker-compose.override.yml.example` to
`devops/dev/docker-compose.override.yml`.  Every place that that has `<path to solution>` will need to be replaced with
the fully qualified path to the base folder of the solution, should be a folder path ending in `stfc` unless you cloned
the repo to a custom folder name.  The project `.gitignore` will not allow this file to be commited to the repo as it is 
dependent on the local environment

From the project root
```shell
cp devops/dev/docker-compose.override.yml.example devops/dev/docker-compose.override.yml
```

### Run the docker solution
The first time that this solution is run it will need to build the project containers and download the base containers 
used in the project.  This initial build can take some time, grab a coffee after executing the up command for the first 
time.

From the project root
```shell
cd devops/dev
docker-compose up -d
```

### Shell access to php container
From the devops/dev directory
```shell
docker-compose exec php bash
```

## Setting Up the Dev Environment

### Installing Project Packages
**This Step Only Needs To Be Done the first time or when the components are updated**

Composer is installed in the project php docker container, when the repo is freshly created none of the packages will be
installed in the `vendor` directory.  We will need composer to add the required vendor packages to the project directory.

From outside the container (from inside the dev directory)
```shell
docker-compose exec php composer install -n
```

From inside the container
```shell script
composer install -n
```

### Setting up the Development Database
This will run all the migrations and load the development fixture data into the database.  Presently this fixture data 
seeds the database for the down stream console commands.

From outside the container (from inside the dev directory)
```shell
docker-compose exec php composer setup
```

From inside the container
```shell script
composer setup
```

## Testing

### Full Test suite run

This command will rebuild the test database, load the fixture data into the test database and run the unit tests. All
within the `APP_ENV=test` context.  This will then use the settings contained in the `.env.test` or `.env.test.local` 
(if present).  It is recommended to create a local `.env.test.local` and have the database connection connect to a 
`test` version of the database so the test running will not affect any data that you have collected in your development
database tables, as this process will drop and recreate the database.  Only fixture data and data generated during 
tests will be left in the database after the tests are run.


From outside the container (from inside the dev directory)
```shell
docker-compose exec php composer tests
```

From inside the container
```shell
composer tests
```

### Only Rerun tests
The test command will not touch the database, and will just run the tests in the `APP_ENV=test` context.  As with the
full test suite run this will use the settings contained in the `.env.test` or `.env.test.local`
(if present).

From outside the container (from inside the dev directory)
```shell
docker-compose exec php composer tests
```

From inside the container
```shell
composer test
```

## Project Scripts

### load: scripts
All the project script are accessed via the symfony console.  The symfony console can be accessed from outside the
containers or from inside the containers using the following commands.

from outside the container (from inside the dev directory)
```shell
docker-compose exec php php bin/console
```

from inside the container
```shell
php bin/console
```

#### Resources
The resource load script will pull the resource json objects from the stfc api.  The resource json objects are summary 
objects that have basic information along with a collection of all the detail resource ids for the individual categories
of resources.  This command needs to be run to collect the json objects for processing by the detail script.

from outside the container (from inside the dev directory)
```shell
docker-compose exec php php bin/console load:resources
```

from inside the container
```shell
php bin/console load:resources
```

#### Details
The detail load script will pull the ids from the resource json and then download the detail request json objects for
the individual resources listed in the corresponding categories' resource json file.  **NOTE:** currently this script is 
hard coded to only pull the system resources, however it can easily be extended to pull additional resource categories.

from outside the container (from inside the dev directory)
```shell
docker-compose exec php php bin/console load:details
```

from inside the container
```shell
php bin/console load:details
```
