-- 1. Customer
LOAD DATA INFILE '/var/lib/mysql-files/data/customer.csv' INTO TABLE Customer FIELDS TERMINATED BY ',' ENCLOSED BY '"' LINES TERMINATED BY '\n' IGNORE 1 ROWS (
    customer_id,
    first_name,
    last_name,
    email,
    SSN,
    password
);

-- 2. Administrator
LOAD DATA INFILE '/var/lib/mysql-files/data/administrator.csv' INTO TABLE Administrator FIELDS TERMINATED BY ',' ENCLOSED BY '"' LINES TERMINATED BY '\n' IGNORE 1 ROWS (
    administrator_id,
    first_name,
    last_name,
    email,
    SSN,
    password
);

-- 3. Account
LOAD DATA INFILE '/var/lib/mysql-files/data/account.csv' INTO TABLE Account FIELDS TERMINATED BY ',' ENCLOSED BY '"' LINES TERMINATED BY '\n' IGNORE 1 ROWS (
    account_id,
    owner_id,
    account_status,
    balance,
    account_history
);

-- 4. Transaction
LOAD DATA INFILE '/var/lib/mysql-files/data/transaction.csv' INTO TABLE `Transaction` FIELDS TERMINATED BY ',' ENCLOSED BY '"' LINES TERMINATED BY '\n' IGNORE 1 ROWS (
    transaction_id,
    account_id,
    name,
    `date`,
    amount,
    new_balance,
    transaction_type
);

-- 5. Bank_Deposit
LOAD DATA INFILE '/var/lib/mysql-files/data/bank_deposit.csv' INTO TABLE Bank_Deposit FIELDS TERMINATED BY ',' ENCLOSED BY '"' LINES TERMINATED BY '\n' IGNORE 1 ROWS (
    deposit_id,
    transaction_id,
    account_id,
    name,
    `date`,
    amount,
    new_balance,
    transaction_type
);

-- 6. Bank_Withdrawal
LOAD DATA INFILE '/var/lib/mysql-files/data/bank_withdrawal.csv' INTO TABLE Bank_Withdrawal FIELDS TERMINATED BY ',' ENCLOSED BY '"' LINES TERMINATED BY '\n' IGNORE 1 ROWS (
    withdrawal_id,
    transaction_id,
    account_id,
    name,
    `date`,
    amount,
    new_balance,
    transaction_type
);

-- 7. Bank_Transfer
LOAD DATA INFILE '/var/lib/mysql-files/data/bank_transfer.csv' INTO TABLE Bank_Transfer FIELDS TERMINATED BY ',' ENCLOSED BY '"' LINES TERMINATED BY '\n' IGNORE 1 ROWS (
    transfer_id,
    transaction_id,
    account_id,
    name,
    `date`,
    amount,
    new_balance,
    transaction_type,
    from_account,
    to_account
);