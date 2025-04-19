<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Bank Withdrawal Form</title>
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
        $errors = array();
        $currentBalance = (float) $account['balance'];
        $acctId = $account['account_id'];
        $name = $_POST['name'];
        $now = date("Y-m-d H:i:s");
        $amount = (float) $_POST['amount'];
        $newBalance = $currentBalance - $amount;
        $transactionType = "Withdraw";

        if(round($newBalance, 2) < round(0, 2)){
            array_push($errors, "Insufficient funds.");
        }

        if(round($amount, 2) < round(0, 2)){
            array_push($errors, "Invalid amount.");
        }

        if (count($errors) > 0) {
          foreach ($errors as $error) {
            echo "<div>$error</div>";
          }
        }
        else{
          $sql = "INSERT INTO Bank_Withdrawal (account_id, name, `date`, amount, new_balance, transaction_type) VALUES (?, ?, ?, ?, ?, ?)";
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
          $sql = "UPDATE Account SET balance = ? WHERE account_id = ?";
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

    }
    ?>
  <h1>Bank Withdrawal Form</h1>
  <form action="#" method="post">
    <!-- transaction_id is auto-generated -->
    <label for="name">Transaction Name/Description:</label>
    <input type="text" id="name" name="name" required>
    <br><br>
    <label for="amount">Amount:</label>
    <input type="number" id="amount" name="amount" step="0.01" required>
    <br><br>
    <input type="submit" value="Withdraw" name="submit">
  </form>
    <br>
    <a href="account.php">Back to Account</a>

</body>
</html>
