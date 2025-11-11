#!/bin/bash

BRANCH=${1:-development}

# Build the container with a known name
docker build --build-arg BRANCH="$BRANCH" -t bookstack:db-testing .
if [ $? -eq 1 ]; then
  echo "Failed to build app container for testing"
  exit 1
fi

# List of database containers to test against
containers=(
  "mysql:5.7"
  "mysql:8.0"
  "mysql:8.4"
  "mysql:9.5"
  "mariadb:10.2"
  "mariadb:10.6"
  "mariadb:10.11"
  "mariadb:11.4"
  "mariadb:11.8"
  "mariadb:12.0"
)

# Pre-clean-up from prior runs
docker stop bs-dbtest-db
docker network rm bs-dbtest-net

# Cycle over each database image
for img in "${containers[@]}"; do
  echo "Starting tests with $img..."
  docker network create bs-dbtest-net
  docker run -d --rm --name "bs-dbtest-db" \
    -e MYSQL_DATABASE=bookstack-test \
    -e MYSQL_USER=bookstack \
    -e MYSQL_PASSWORD=bookstack \
    -e MYSQL_ROOT_PASSWORD=password \
	  --network=bs-dbtest-net \
    "$img"
  sleep 20
  APP_RUN='docker run -it --rm --network=bs-dbtest-net -e TEST_DATABASE_URL="mysql://bookstack:bookstack@bs-dbtest-db:3306" bookstack:db-testing'
  $APP_RUN artisan migrate --force --database=mysql_testing
  $APP_RUN artisan db:seed --force --class=DummyContentSeeder --database=mysql_testing
  $APP_RUN vendor/bin/phpunit
  if [ $? -eq 0 ]; then
    echo "$img - Success"
  else
    echo "$img - Error"
    read -p "Stop script? [y/N] " ans
    [[ $ans == [yY] ]] && exit 0
  fi

  docker stop "bs-dbtest-db"
  docker network rm bs-dbtest-net
done

