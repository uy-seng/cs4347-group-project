<?php
session_start();
if (!isset($_SESSION["user"])) {
  header("Location: login.php");
  exit;
}

$user = $_SESSION["user"];
$errors = array();
require_once "database.php";

if (isset($_POST["submit"])) {
  $sql = "SELECT * FROM Account WHERE owner_id = ?";
  $stmt = mysqli_prepare($conn, $sql);
  mysqli_stmt_bind_param($stmt, "i", $user["customer_id"]);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  $fromAccount = mysqli_fetch_assoc($result);
  $completeTransfer = "";

  if (!$fromAccount) {
    array_push($errors, "Account not found");
  } else {
    $name = $_POST['name'];
    $amount = (float) $_POST['amount'];
    $toAccountID = (int) $_POST['to_account'];
    $fromAccountID = $fromAccount['account_id'];
    $currentBalance = (float) $fromAccount['balance'];
    $now = date("Y-m-d H:i:s");

    if (round($amount, 2) < 0) {
      array_push($errors, "Transfer amount not valid.");
    }
    if (round($currentBalance, 2) < round($amount, 2)) {
      array_push($errors, "Insufficient funds for this transfer.");
    }
    if ($toAccountID == $fromAccountID) {
      array_push($errors, "Cannot transfer to the same account.");
    } else {
      $sql = "SELECT * FROM Account WHERE account_id = ?";
      $stmt = mysqli_prepare($conn, $sql);
      mysqli_stmt_bind_param($stmt, "i", $toAccountID);
      mysqli_stmt_execute($stmt);
      $result = mysqli_stmt_get_result($stmt);
      $toAccount = mysqli_fetch_assoc($result);

      if (!$toAccount or $toAccount === 0) {
        array_push($errors, "Destination Account not found");
      } else if ($toAccount['account_status'] !== 'active') {
        array_push($errors, "Destination Account not active");
      }
    }

    if (count($errors) === 0) {
      mysqli_begin_transaction($conn);
      $fromNewBalance = $currentBalance - $amount;
      $toNewBalance = (float) $toAccount['balance'] + $amount;
      $transactionType = "Transfer";

      $sql = "INSERT INTO Transaction (account_id, name, `date`, amount, new_balance, transaction_type) VALUES (?, ?, ?, ?, ?, ?)";
      $stmt = mysqli_prepare($conn, $sql);
      mysqli_stmt_bind_param($stmt, "issdss", $fromAccountID, $name, $now, $amount, $fromNewBalance, $transactionType);
      mysqli_stmt_execute($stmt);

      $transactionID = mysqli_insert_id($conn);

      $sql = "INSERT INTO Bank_Transfer (transaction_id, account_id, name, `date`, amount, new_balance, transaction_type, from_account, to_account) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
      $stmt = mysqli_prepare($conn, $sql);
      mysqli_stmt_bind_param($stmt, "iissddsii", $transactionID, $fromAccountID, $name, $now, $amount, $fromNewBalance, $transactionType, $fromAccountID, $toAccountID);
      mysqli_stmt_execute($stmt);

      $sql = "SELECT account_history FROM Account WHERE account_id = ?";
      $stmt = mysqli_prepare($conn, $sql);
      mysqli_stmt_bind_param($stmt, "i", $fromAccountID);
      mysqli_stmt_execute($stmt);
      $historyResult = mysqli_stmt_get_result($stmt);
      $historyData = mysqli_fetch_assoc($historyResult);
      $curHistory = $historyData['account_history'] ?? "";
      $updateHistory = empty($curHistory) ? $transactionID : $transactionID . " " . $curHistory;

      $sql = "UPDATE Account SET balance = ?, account_history = ? WHERE account_id = ?";
      $stmt = mysqli_prepare($conn, $sql);
      mysqli_stmt_bind_param($stmt, "dsi", $fromNewBalance, $updateHistory, $fromAccountID);
      mysqli_stmt_execute($stmt);

      $sql = "SELECT account_history FROM Account WHERE account_id = ?";
      $stmt = mysqli_prepare($conn, $sql);
      mysqli_stmt_bind_param($stmt, "i", $toAccountID);
      mysqli_stmt_execute($stmt);
      $historyResult = mysqli_stmt_get_result($stmt);
      $historyData = mysqli_fetch_assoc($historyResult);
      $curHistory = $historyData['account_history'] ?? "";
      $updateHistory = empty($curHistory) ? $transactionID : $transactionID . " " . $curHistory;

      $sql = "UPDATE Account SET balance = ?, account_history = ? WHERE account_id = ?";
      $stmt = mysqli_prepare($conn, $sql);
      mysqli_stmt_bind_param($stmt, "dsi", $toNewBalance, $updateHistory, $toAccountID);
      mysqli_stmt_execute($stmt);

      mysqli_commit($conn);
      $completeTransfer = "Transfer complete. $" . number_format($amount, 2) . " transfered to account #" . $toAccountID;
      mysqli_stmt_close($stmt);
    }
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Bank Transfer Form</title>
</head>

<body>
  <h1>Bank Transfer Form</h1>
  <?php
  if (count($errors) > 0) {
    foreach ($errors as $error) {
      echo "<div>$error</div>";
    }
  }

  if (!empty($completeTransfer)) {
    echo "<div>$completeTransfer</div>";
  }
  ?>
  <form action="#" method="post">
    <!-- transaction_id is auto-generated -->
    <label for="name">Transaction Name/Description:</label>
    <input type="text" id="name" name="name">
    <br><br>
    <label for="amount">Amount:</label>
    <input type="number" id="amount" name="amount" step="0.01">
    <br><br>
    <label for="to_account">To Account:</label>
    <input type="number" id="to_account" name="to_account">
    <br><br>
    <input type="submit" value="Transfer" name="submit">
  </form>
  <br>
  <a href="account.php">Back To Account</a>
</body>

</html>