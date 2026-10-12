#!/bin/bash

BRANCH=development
SKIP_BUILD=0

# Parse arguments: --skip-build, plus an optional branch name
for arg in "$@"; do
  case "$arg" in
    --skip-build)
      SKIP_BUILD=1
      ;;
    *)
      BRANCH="$arg"
      ;;
  esac
done

# Use podman if available, otherwise fall back to docker
if command -v podman >/dev/null 2>&1; then
  ENGINE=podman
elif command -v docker >/dev/null 2>&1; then
  ENGINE=docker
else
  echo "Failed to find podman or docker in PATH"
  exit 1
fi

# Build the container with a known name
if [ "$SKIP_BUILD" -eq 1 ]; then
  echo "Skipping app container build"
else
  $ENGINE build --no-cache --build-arg BRANCH="$BRANCH" -t bookstack:db-testing .
  if [ $? -ne 0 ]; then
    echo "Failed to build app container for testing"
    exit 1
  fi
fi

# List of database containers to test against
containers=(
  "docker.io/library/mysql:8.0"
  "docker.io/library/mysql:8.4"
  "docker.io/library/mysql:9.5"
  "docker.io/library/mariadb:10.6"
  "docker.io/library/mariadb:10.11"
  "docker.io/library/mariadb:11.4"
  "docker.io/library/mariadb:11.8"
  "docker.io/library/mariadb:12.0"
)

# Pre-clean-up from prior runs
$ENGINE stop bs-dbtest-db
$ENGINE network rm bs-dbtest-net

# Cycle over each database image
for img in "${containers[@]}"; do
  echo "Starting tests with $img..."
  $ENGINE network create bs-dbtest-net
  $ENGINE run -d --rm --name "bs-dbtest-db" \
    -e MYSQL_DATABASE=bookstack-test \
    -e MYSQL_USER=bookstack \
    -e MYSQL_PASSWORD=bookstack \
    -e MYSQL_ROOT_PASSWORD=password \
	  --network=bs-dbtest-net \
    "$img"
  sleep 20
  APP_RUN="$ENGINE run -it --rm --network=bs-dbtest-net -e TEST_DATABASE_URL=\"mysql://bookstack:bookstack@bs-dbtest-db:3306\" bookstack:db-testing"
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

  $ENGINE stop "bs-dbtest-db"
  $ENGINE network rm bs-dbtest-net
done
