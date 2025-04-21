<?php
session_start();

// make sure an administrator is logged in
if (
    !isset($_SESSION['user']) ||
    !isset($_SESSION['user']['administrator_id'])
) {
    header('Location: login.php');
    exit();
}
$admin = $_SESSION['user'];

require_once 'database.php';

// handle balance modification
if (isset($_POST['modify'])) {
    $account_id = intval($_POST['account_id']);
    $new_balance = floatval($_POST['new_balance']);
    $stmt = $conn->prepare("
        UPDATE Account
           SET balance = ?
         WHERE account_id = ?
    ");
    $stmt->bind_param("di", $new_balance, $account_id);
    $stmt->execute();
    $stmt->close();
}

// handle account deletion
if (isset($_POST['delete'])) {
    $account_id = intval($_POST['account_id']);
    $stmt = $conn->prepare("
        DELETE FROM Account
         WHERE account_id = ?
    ");
    $stmt->bind_param("i", $account_id);
    $stmt->execute();
    $stmt->close();
}

// fetch all accounts with customer names
$sql = "
    SELECT
        a.account_id,
        a.owner_id,
        CONCAT(c.first_name, ' ', c.last_name) AS customer_name,
        a.balance,
        a.account_status
      FROM Account a
 LEFT JOIN Customer c
        ON a.owner_id = c.customer_id
  ORDER BY a.account_id
";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin – Account Management</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            padding: 8px;
            border: 1px solid #ccc;
        }

        input[type="number"] {
            width: 80px;
        }
    </style>
</head>

<body>
    <h2>Admin ID: <?php echo htmlspecialchars($admin['administrator_id']); ?></h2>

    <form method="post" action="admin_information.php">
        <table>
            <thead>
                <tr>
                    <th>Select</th>
                    <th>Account ID</th>
                    <th>Owner ID</th>
                    <th>Customer Name</th>
                    <th>Balance</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <input type="radio" name="account_id" value="<?php echo $row['account_id']; ?>" required>
                        </td>
                        <td><?php echo $row['account_id']; ?></td>
                        <td><?php echo $row['owner_id']; ?></td>
                        <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                        <td><?php echo number_format($row['balance'], 2); ?></td>
                        <td><?php echo htmlspecialchars($row['account_status']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <p>
            <label for="new_balance">New Balance:</label>
            <input type="number" step="0.01" name="new_balance" id="new_balance" placeholder="0.00" required>
        </p>

        <button type="submit" name="modify">Modify Balance</button>
        <button type="submit" name="delete" onclick="return confirm('Are you sure you want to delete this account?');">
            Delete Account
        </button>
    </form>
</body>

</html>