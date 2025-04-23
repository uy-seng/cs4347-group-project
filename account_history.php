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

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Account History</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }

    .card {
      margin: 40px auto;
      padding: 30px;
      max-width: 900px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    table {
      margin-top: 20px;
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="card">
      <h1 class="text-center mb-4 text-primary">Account History</h1>

      <?php if (empty($accountHistory)): ?>
        <p class="text-center text-muted">No Account History</p>
      <?php else: ?>
        <table class="table table-striped table-bordered">
          <thead class="table-primary">
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
                $formatAmount = $negative ? "-$" . $formatAmount : "$" . $formatAmount;

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

      <div class="text-center mt-4">
        <a class="btn btn-outline-primary" href="account.php">Back to Account</a>
      </div>
    </div>
  </div>
</body>

</html>