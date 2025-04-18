-- Load data into Customer table
-- Assuming customer.csv contains: first_name,last_name,email,SSN,password
LOAD DATA INFILE '/var/lib/mysql-files/data/customer.csv' INTO TABLE Customer FIELDS TERMINATED BY ',' ENCLOSED BY '"' LINES TERMINATED BY '\n' IGNORE 1 ROWS (first_name, last_name, email, SSN, password);

-- -- Load data into Administrator table
-- -- Assuming administrator.csv contains: customer_id,first_name,last_name,email,SSN,password
LOAD DATA INFILE '/var/lib/mysql-files/data/administrator.csv' INTO TABLE Administrator FIELDS TERMINATED BY ',' ENCLOSED BY '"' LINES TERMINATED BY '\n' IGNORE 1 ROWS (
    administrator_id,
    first_name,
    last_name,
    email,
    SSN,
    password
);

-- -- Load data into Account table
-- -- Assuming account.csv contains: owner_id,account_status,balance,account_history
LOAD DATA INFILE '/var/lib/mysql-files/data/account.csv' INTO TABLE Account FIELDS TERMINATED BY ',' ENCLOSED BY '"' LINES TERMINATED BY '\n' IGNORE 1 ROWS (
    owner_id,
    account_status,
    balance,
    account_history
);

-- -- Load data into Transaction table
-- -- Assuming transaction.csv contains: account_id,name,date,amount,new_balance,transaction_type
LOAD DATA INFILE '/var/lib/mysql-files/data/transaction.csv' INTO TABLE Transaction FIELDS TERMINATED BY ',' ENCLOSED BY '"' LINES TERMINATED BY '\n' IGNORE 1 ROWS (
    account_id,
    name,
    `date`,
    amount,
    new_balance,
    transaction_type
);

-- -- Load data into Bank_Withdrawal table
-- -- Assuming bank_withdrawal.csv contains: account_id,name,date,amount,new_balance,transaction_type
LOAD DATA INFILE '/var/lib/mysql-files/data/bank_withdrawal.csv' INTO TABLE Bank_Withdrawal FIELDS TERMINATED BY ',' ENCLOSED BY '"' LINES TERMINATED BY '\n' IGNORE 1 ROWS (
    account_id,
    name,
    `date`,
    amount,
    new_balance,
    transaction_type
);

-- -- Load data into Bank_Deposit table
-- -- Assuming bank_deposit.csv contains: account_id,name,date,amount,new_balance,transaction_type
LOAD DATA INFILE '/var/lib/mysql-files/data/bank_deposit.csv' INTO TABLE Bank_Deposit FIELDS TERMINATED BY ',' ENCLOSED BY '"' LINES TERMINATED BY '\n' IGNORE 1 ROWS (
    account_id,
    name,
    `date`,
    amount,
    new_balance,
    transaction_type
);

-- -- Load data into Bank_Transfer table
-- -- Assuming bank_transfer.csv contains: account_id,name,date,amount,new_balance,transaction_type,from_account,to_account
-- LOAD DATA INFILE '/var/lib/mysql-files/data/bank_transfer.csv' INTO TABLE Bank_Transfer FIELDS TERMINATED BY ',' ENCLOSED BY '"' LINES TERMINATED BY '\n' IGNORE 1 ROWS (
--     account_id,
--     name,
--     `date`,
--     amount,
--     new_balance,
--     transaction_type,
--     from_account,
--     to_account
-- );