# stfc

**WARNING:** if you are deploying on a Windows machine avoid cloning the repo into a subdirectory of your Documents
folder.  Windows does some very heavy simlinking to that directory, and it usually confuses docker.  Clone the repo to a
simple directory, like `c:\projects\stfc`

## About
This is an attempt to collect data about star trek fleet command through their public api.  The end goal of which is to
create tools to help with the game play mechanics.


## Project Setup

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

### Setting up the Development Database
This will run all the migrations and load the development fixture data into the database.

From outside the container
```shell
docker-compose exec php composer setup
```

From inside the container
```shell script
composer setup
```


## Project Scripts

## Testing

### Full Test suite run

This command will rebuild the test database, load the fixture data into the test database and run the unit tests. All
within the `APP_ENV=test` context.

From outside the container
```shell
docker-compose exec php composer tests
```

From inside the container
```shell
composer tests
```

### Only Rerun tests
The test command will not touch the database, and will just run the tests in the `APP_ENV=test` context
From outside the container
```shell
docker-compose exec php composer tests
```

From inside the container
```shell
composer test
```