# Database Testing Suite

This docker setup is designed to run BookStack's test suite against each major database version we support
across MySQL and MariaDB to ensure compatibility and highlight any potential issues before a release.
This is a fairly slow and heavy process, so is designed to just be run manually before a release which
makes changes to the database schema, or a release which makes significant changes to database queries.

### Running

Everything is ran via the `run.sh` script. This will:

- Optionally, accept a branch of BookStack to use for testing.
- Build the docker image from the `Dockerfile`.
  - This will include a built-in copy of the chosen BookStack branch. 
- Cycle through each major supported database version:
  - Migrate and seed the database.
  - Run the full PHP test suite.

If there's a failure for a database version, the script will prompt if you'd like to continue or stop testing.

This script should be ran from this `db-testing` directory:

```bash
# Enter this directory
cd dev/docker/db-testing

# Runs for the 'development' branch by default
./run.sh

# Run for a specific branch
./run.sh v25-11
```
