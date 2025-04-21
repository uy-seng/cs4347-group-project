-- Create database and use it
CREATE DATABASE IF NOT EXISTS cs4347_group_project;

USE cs4347_group_project;

-- Drop tables if they already exist (order matters because of FK constraints)
DROP TABLE IF EXISTS Bank_Transfer;

DROP TABLE IF EXISTS Bank_Deposit;

DROP TABLE IF EXISTS Bank_Withdrawal;

DROP TABLE IF EXISTS Transaction;

DROP TABLE IF EXISTS Account;

DROP TABLE IF EXISTS Administrator;

DROP TABLE IF EXISTS Customer;

-- Create Customer table
CREATE TABLE Customer (
    customer_id INT NOT NULL AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    SSN VARCHAR(20) NOT NULL UNIQUE,
    password VARCHAR(100) NOT NULL,
    PRIMARY KEY (customer_id)
);

-- -- Create Administrator table
CREATE TABLE Administrator (
    administrator_id INT NOT NULL AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    SSN VARCHAR(20) NOT NULL UNIQUE,
    password VARCHAR(100) NOT NULL,
    PRIMARY KEY (administrator_id)
);

-- -- Create Account table
CREATE TABLE Account (
    account_id INT NOT NULL AUTO_INCREMENT,
    owner_id INT NOT NULL,
    account_status VARCHAR(20) NOT NULL DEFAULT 'active',
    balance DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    account_history TEXT,
    PRIMARY KEY (account_id),
    FOREIGN KEY (owner_id) REFERENCES Customer(customer_id) ON DELETE CASCADE
);

-- -- Create Transaction table
CREATE TABLE Transaction (
    transaction_id INT NOT NULL AUTO_INCREMENT,
    account_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    amount DECIMAL(10, 2) NOT NULL,
    new_balance DECIMAL(10, 2) NOT NULL,
    transaction_type VARCHAR(20) NOT NULL,
    PRIMARY KEY (transaction_id),
    FOREIGN KEY (account_id) REFERENCES Account(account_id) ON DELETE NO ACTION
);

-- -- Create Bank_Withdrawal table
CREATE TABLE Bank_Withdrawal (
    transaction_id INT NOT NULL,
    withdrawal_id INT NOT NULL AUTO_INCREMENT,
    account_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    amount DECIMAL(10, 2) NOT NULL,
    new_balance DECIMAL(10, 2) NOT NULL,
    transaction_type VARCHAR(20) NOT NULL,
    PRIMARY KEY (withdrawal_id),
    FOREIGN KEY (account_id) REFERENCES Account(account_id) ON DELETE NO ACTION,
    FOREIGN KEY (transaction_id) REFERENCES Transaction(transaction_id) ON DELETE CASCADE
);

-- -- Create Bank_Deposit table
CREATE TABLE Bank_Deposit (
    transaction_id INT NOT NULL,
    deposit_id INT NOT NULL AUTO_INCREMENT,
    account_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    amount DECIMAL(10, 2) NOT NULL,
    new_balance DECIMAL(10, 2) NOT NULL,
    transaction_type VARCHAR(20) NOT NULL,
    PRIMARY KEY (deposit_id),
    FOREIGN KEY (account_id) REFERENCES Account(account_id) ON DELETE NO ACTION,
    FOREIGN KEY (transaction_id) REFERENCES Transaction(transaction_id) ON DELETE CASCADE
);

-- -- Create Bank_Transfer table
CREATE TABLE Bank_Transfer (
    transaction_id INT NOT NULL,
    transfer_id INT NOT NULL AUTO_INCREMENT,
    account_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    amount DECIMAL(10, 2) NOT NULL,
    new_balance DECIMAL(10, 2) NOT NULL,
    transaction_type VARCHAR(20) NOT NULL,
    from_account INT NULL,
    to_account INT NULL,
    PRIMARY KEY (transfer_id),
    FOREIGN KEY (transaction_id) REFERENCES Transaction(transaction_id) ON DELETE CASCADE,
    FOREIGN KEY (account_id) REFERENCES Account(account_id) ON DELETE NO ACTION,
    FOREIGN KEY (from_account) REFERENCES Account(account_id) ON DELETE
    SET
        NULL,
        FOREIGN KEY (to_account) REFERENCES Account(account_id) ON DELETE
    SET
        NULL
);