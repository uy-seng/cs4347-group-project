<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Administrator Form</title>
</head>

<body>
    <?php
    session_start();
    require_once "database.php";
    // check if user is logged in
    if (!isset($_SESSION["user"])) {
        header('HTTP/1.1 401 Unauthorized');
        exit('Error: User not logged in.');
    }

    if (isset($_POST["submit"])) {
        echo "<div>Form submitted</div>";

        $user = $_SESSION["user"];
        // get user account
        $sql = "SELECT * FROM Account WHERE owner_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $user["customer_id"]);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $account = mysqli_fetch_assoc($result);

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

        // insert deposit record
        $sql = "
      INSERT INTO Bank_Deposit 
        (account_id, name, `date`, amount, new_balance, transaction_type)
      VALUES (?, ?, ?, ?, ?, ?)
    ";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param(
            $stmt,
            "issdss",
            $acctId,
            $name,
            $now,
            $amount,
            $newBalance,
            $transactionType
        );
        mysqli_stmt_execute($stmt);

        // check if INSERT succeeded
        if (mysqli_stmt_affected_rows($stmt) <= 0) {
            header('HTTP/1.1 500 Internal Server Error');
            exit('Error: Failed to insert deposit.');
        }
        mysqli_stmt_close($stmt);

        // update account balance
        $sql = "
      UPDATE Account
         SET balance = ?
       WHERE account_id = ?
    ";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "di", $newBalance, $acctId);
        mysqli_stmt_execute($stmt);

        // check if UPDATE succeeded
        if (mysqli_stmt_affected_rows($stmt) <= 0) {
            header('HTTP/1.1 500 Internal Server Error');
            exit('Error: Failed to update balance.');
        }
        mysqli_stmt_close($stmt);

        // 3. Redirect on success
        header("Location: account.php");
        exit();
    }
    ?>

    <h1>Bank Deposit Form</h1>
    <form action="bank_deposit.php" method="post">
        <!-- transaction_id is auto-generated -->
        <label for="name">Transaction Name/Description:</label>
        <input type="text" id="name" name="name" required>
        <br><br>
        <label for="amount">Amount:</label>
        <input type="number" id="amount" name="amount" step="0.01" required>
        <br><br>
        <button type="submit" name="submit">Confirm</button>
    </form>
</body>

</html>