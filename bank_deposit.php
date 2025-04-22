<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Administrator Form</title>
</head>

<body>
    <?php
    session_start();
    $errors = array();
    require_once "database.php";
    // check if user is logged in
    if (!isset($_SESSION["user"])) {
        header('HTTP/1.1 401 Unauthorized');
        exit('Error: User not logged in.');
    }

    if (isset($_POST["submit"])) {

        $user = $_SESSION["user"];
        // get user account
        $sql = "SELECT * FROM Account WHERE owner_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $user["customer_id"]);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $account = mysqli_fetch_assoc($result);
        $completeTransaction = "";

        // check if account exists
        if (!$account) {
            header('HTTP/1.1 404 Not Found');
            exit('Error: Account not found.');
        }

        // compute new balance
        $currentBalance = (float) $account['balance'];
        $acctId = $account['account_id'];
        $name = $_POST['name'];
        $now = date("Y-m-d H:i:s");
        $amount = (float) $_POST['amount'];
        $newBalance = $currentBalance + $amount;
        $transactionType = "Deposit";


        if (round($amount, 2) < round(0, 2)) {
            array_push($errors, "Invalid amount.");
        }

        if (count($errors) === 0) {
            mysqli_begin_transaction($conn);

            // insert deposit record
            $sql = "INSERT INTO Transaction (account_id, name, `date`, amount, new_balance, transaction_type) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "issdss", $acctId, $name, $now, $amount, $newBalance, $transactionType);
            mysqli_stmt_execute($stmt);

            $transactionID = mysqli_insert_id($conn);

            // check if INSERT succeeded
            if (mysqli_stmt_affected_rows($stmt) <= 0) {
                header('HTTP/1.1 500 Internal Server Error');
                exit('Error: Failed to insert deposit.');
            }
            mysqli_stmt_close($stmt);

            $sql = "INSERT INTO Bank_Deposit (transaction_id, account_id, name, `date`, amount, new_balance, transaction_type) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "iissdss", $transactionID, $acctId, $name, $now, $amount, $newBalance, $transactionType);
            mysqli_stmt_execute($stmt);

            if (mysqli_stmt_affected_rows($stmt) <= 0) {
                header('HTTP/1.1 500 Internal Server Error');
                exit('Error: Failed to insert deposit.');
            }

            mysqli_commit($conn);

            $sql = "SELECT account_history FROM Account WHERE account_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $acctId);
            mysqli_stmt_execute($stmt);
            $historyResult = mysqli_stmt_get_result($stmt);
            $historyData = mysqli_fetch_assoc($historyResult);
            $curHistory = $historyData['account_history'] ?? "";
            $updateHistory = empty($curHistory) ? $transactionID : $transactionID . " " . $curHistory;

            // update account balance and history
            $sql = "UPDATE Account SET balance = ?, account_history = ? WHERE account_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "dsi", $newBalance, $updateHistory, $acctId);
            mysqli_stmt_execute($stmt);

            // check if UPDATE succeeded
            if (mysqli_stmt_affected_rows($stmt) <= 0) {
                header('HTTP/1.1 500 Internal Server Error');
                exit('Error: Failed to update balance.');
            }
            $completeTransaction = "Transaction complete. $" . number_format($amount, 2) . " deposited to account #" . $acctId;
            mysqli_stmt_close($stmt);
        }
    }

    ?>

    <h1>Bank Deposit Form</h1>
    <?php
    if (count($errors) > 0) {
        foreach ($errors as $error) {
            echo "<div>$error</div>";
        }
    }

    if (!empty($completeTransaction)) {
        echo "<div>$completeTransaction</div>";
    }
    ?>
    <form action="bank_deposit.php" method="post">
        <!-- transaction_id is auto-generated -->
        <label for="name">Transaction Name/Description:</label>
        <input type="text" id="name" name="name" required>
        <br><br>
        <label for="amount">Amount:</label>
        <input type="number" id="amount" name="amount" step="0.01" required>
        <br><br>
        <input type="submit" value="Deposit" name="submit">
    </form>
    <br>
    <a href="account.php">Back to Account</a>
</body>


</html>