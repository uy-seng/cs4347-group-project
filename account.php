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
</head>

<body>
  <h1>Welcome <?php echo htmlspecialchars($user["first_name"]) ?></h1>
  <!-- account_id is auto-generated -->
  <div>
    <p>Account Number: <?php echo htmlspecialchars($account["account_id"]) ?></p>
    <p>Account Status: <?php echo htmlspecialchars($account["account_status"]) ?></p>
    <p>Balance: <?php echo htmlspecialchars($account["balance"]) ?></p>
  </div>
  <a href="bank_withdrawal.html"><button>Withdrawal</button></a>
  <a href="bank_deposit.php"><button>Deposit</button></a>
  <a href="bank_transfer.html"><button>Transfer</button></a>
  <a href="account_history.html"><button>Account History</button></a>
  <br><br>
  <a href="logout.php"><button>Logout</button></a>
</body>

</html>