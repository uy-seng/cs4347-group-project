<?php
session_start();
if (!isset($_SESSION["user"])) {
  header("Location: login.php");
  exit;
}

$user = $_SESSION["user"];

require_once "database.php";
$customerID = $user["customer_id"];

$sql = "SELECT * FROM Account WHERE Account.owner_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $customerID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$account = mysqli_fetch_array($result, MYSQLI_ASSOC);

if (!$account) {
  $account = [
    "account_id" => "Not created yet",
    "account_status" => "inactive",
    "balance" => "0.00"
  ];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Account Information</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
  <div class="container mt-5">
    <h1 class="mb-4 text-primary">Welcome, <?php echo htmlspecialchars($user["first_name"]) ?></h1>

    <div class="card shadow-sm mb-4">
      <div class="card-body">
        <h5 class="card-title ">Account Information</h5>
        <p class="card-text "><strong>Account Number:</strong> <?php echo htmlspecialchars($account["account_id"]) ?>
        </p>
        <p class="card-text"><strong>Account Status:</strong> <?php echo htmlspecialchars($account["account_status"]) ?>
        </p>
        <p class="card-text"><strong>Balance:</strong> $<?php echo htmlspecialchars($account["balance"]) ?></p>
      </div>
    </div>

    <div class="mb-3">
      <a href="bank_withdrawal.php" class="btn btn-primary me-2 mb-2">Withdrawal</a>
      <a href="bank_deposit.php" class="btn btn-primary me-2 mb-2">Deposit</a>
      <a href="bank_transfer.php" class="btn btn-primary me-2 mb-2">Transfer</a>
      <a href="account_history.php" class="btn btn-primary me-2 mb-2">Account History</a>
    </div>

    <a href="logout.php" class="btn btn-outline-danger">Logout</a>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>