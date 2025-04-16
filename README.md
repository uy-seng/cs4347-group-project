# Run using Docker

This document describes how to run a MySQL Docker container, load a database schema and CSV data files, and verify that the tables have been created and populated correctly.

## Prerequisites

- Docker is installed on your system.
- Your project directory (on your host) contains the following:
  - create.sql (the database schema creation script)
  - load.sql (the data loading script)
  - A folder named data that contains your CSV files (e.g., customer.csv, administrator.csv, account.csv, etc.)

## Steps

### 1. Pull the Latest MySQL Docker Image

docker pull mysql:latest

### 2. Start a New MySQL Container

Replace /path/to/your/project with the absolute path to your project directory containing create.sql, load.sql, and the data folder. Also, replace yourpassword with your chosen MySQL root password.

docker run --name test-mysql -v /path/to/your/project:/var/www/html -e MYSQL_ROOT_PASSWORD=yourpassword -d mysql:latest

### 3. Enter the Container Shell

Open a shell in the running container:

docker exec -it test-mysql bash

### 4. Log in to MySQL

Within the container, log in to MySQL as the root user:

mysql -u root -p

Enter your root password when prompted.

### 5. Check the secure_file_priv Setting

Run the following command to see the directory that MySQL is allowed to load files from:

SHOW VARIABLES LIKE 'secure_file_priv';

Note the output directory path (for example, /var/lib/mysql-files/).

### 6. Exit MySQL

Exit the MySQL shell by running:

exit

### 7. Copy the CSV Files to the Secure Directory

Inside the container, copy your data folder (which contains all CSV files) into the directory specified by the secure_file_priv variable. For example:

cp -R /var/www/html/data /var/lib/mysql-files/

### 8. Reconnect to MySQL

Log in to MySQL again:

mysql -u root -p

### 9. Create the Database Schema

Run your schema creation script:

source /var/www/html/create.sql;

### 10. Load the Data from CSV Files

Run your data loading script:

source /var/www/html/load.sql;

### 11. Verify the Tables and Data

To ensure everything has been set up correctly, run the following:

- List all tables:

  SHOW TABLES;

- Preview data in the Customer table:

  SELECT \* FROM Customer LIMIT 10;

If you see your tables listed and the Customer table shows data, then the load was successful!

## Troubleshooting

- File Permissions:
  Ensure the CSV files in /var/lib/mysql-files/ have the correct read permissions.

- CSV Format:
  Verify that the CSV headers match the columns specified in your LOAD DATA statements in load.sql.

- Volume Mapping:
  Confirm that the host directory you specified with the -v option is correctly mapped inside the container.
