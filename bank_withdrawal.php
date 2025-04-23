<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Bank Withdrawal Form</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">

  <link href="styles.css" rel="stylesheet">
</head>

<body>
  <?php
  session_start();
  $errors = array();
  require_once "database.php";
  // check if user is logged in
  if (!isset($_SESSION["user"])) {
    header('HTTP/1.1 401 Unauthorized');
    exit;
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
    $newBalance = $currentBalance - $amount;
    $transactionType = "Withdrawal";

    if (round($newBalance, 2) < round(0.00, 2)) {
      array_push($errors, "You do not have sufficient funds.");
    }
    if (round($amount, 2) < round(0.00, 2)) {
      array_push($errors, "Invalid amount");
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

      $sql = "INSERT INTO Bank_Withdrawal (transaction_id, account_id, name, `date`, amount, new_balance, transaction_type) VALUES (?, ?, ?, ?, ?, ?, ?)";
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

      $completeTransaction = "Transaction complete. $" . number_format($amount, 2) . " withdrawn from account #" . $acctId;
      mysqli_stmt_close($stmt);
    }
  }
  ?>

  <div class="container">
    <h1 class="mb-4 text-primary">Bank Withdrawal</h1>
    <?php
    if (count($errors) > 0) {
      foreach ($errors as $error) {
        echo "<div class='alert alert-danger'>$error</div>";
      }
    }

    if (!empty($completeTransaction)) {
      echo "<div class='alert alert-success'>$completeTransaction</div>";
    }
    ?>
    <form action="bank_withdrawal.php" method="post" class="mb-4">
      <!-- transaction_id is auto-generated -->

      <div class="form-group">
        <label for="name" class="form-label">Transaction Name/Description:</label>
        <input class="form-control" type="text" placeholder="Groceries" id="name" name="name" required>
      </div>
      <div class="form-group">
        <label for="amount" class="form-label">Amount:</label>
        <input class="form-control" type="text" placeholder="100.00" id="amount" name="amount" required>
      </div>
      <div class="form-group">
        <input type="submit" value="Withdrawal" name="submit" class="btn btn-primary">
      </div>
    </form>
    <a href="account.php" class="btn btn-outline-secondary">Back to Account</a>
  </div>
</body>

</html>