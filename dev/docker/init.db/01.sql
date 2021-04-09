# create test database
CREATE DATABASE IF NOT EXISTS `bookstack-test`;

# grant rights
GRANT ALL PRIVILEGES ON `bookstack-test`.* TO 'bookstack-test'@'%';
