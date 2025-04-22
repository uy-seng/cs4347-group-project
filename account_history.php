<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Account History</title>
</head>

<body>
  <?php
  session_start();
  require_once "database.php";

  if (!isset($_SESSION["user"])) {
    header('HTTP/1.1 401 Unauthorized');
    exit('Error: User not logged in.');
  }

  $user = $_SESSION["user"];

  $sql = "SELECT * FROM Account WHERE owner_id = ?";
  $stmt = mysqli_prepare($conn, $sql);
  mysqli_stmt_bind_param($stmt, "i", $user["customer_id"]);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  $account = mysqli_fetch_assoc($result);

  if (!$account) {
    header('HTTP/1.1 404 Not Found');
    exit('Error: Account not found.');
  }

  $accountID = $account['account_id'];
  $accountHistory = $account['account_history'] ?? '';
  ?>

  <h1>Account History</h1>

  <?php if (empty($accountHistory)): ?>
    <p>No Account History</p>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>Transaction Name</th>
          <th>Transaction Date</th>
          <th>Transaction Type</th>
          <th>Transaction Amount</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $transactionIDs = explode(' ', trim($accountHistory));

        foreach ($transactionIDs as $transactionID) {
          if (empty($transactionID))
            continue;

          $sql = "SELECT * FROM Transaction WHERE transaction_id = ?";
          $stmt = mysqli_prepare($conn, $sql);
          mysqli_stmt_bind_param($stmt, "i", $transactionID);
          mysqli_stmt_execute($stmt);
          $transactionResult = mysqli_stmt_get_result($stmt);
          $transaction = mysqli_fetch_assoc($transactionResult);

          if ($transaction) {
            $transactionName = htmlspecialchars($transaction['name']);
            $transactionType = htmlspecialchars($transaction['transaction_type']);
            $transactionDate = htmlspecialchars($transaction['date']);
            $transactionAmount = $transaction['amount'];
            $negative = false;

            if ($transactionType === "Withdrawal" || ($transactionType === "Transfer" && $transaction['account_id'] === $accountID)) {
              $negative = true;
            }

            $formatAmount = number_format($transactionAmount, 2);
            if ($negative) {
              $formatAmount = "-$" . $formatAmount;
            } else {
              $formatAmount = "$" . $formatAmount;
            }

            echo "<tr>";
            echo "<td>$transactionName</td>";
            echo "<td>$transactionDate</td>";
            echo "<td>$transactionType</td>";
            echo "<td>$formatAmount</td>";
            echo "</tr>";
          }
        }
        ?>
      </tbody>
    </table>
  <?php endif; ?>
  <br>
  <a href="account.php">Back to Account</a>
</body>

</html>