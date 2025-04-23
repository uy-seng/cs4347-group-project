<?php
session_start();
if (
    !isset($_SESSION['user']) ||
    !isset($_SESSION['user']['administrator_id'])
) {
    header('Location: login.php');
    exit();
}
$admin = $_SESSION['user'];
require_once 'database.php';

if (isset($_POST['modify'])) {
    $account_id = intval($_POST['account_id']);
    $new_balance = isset($_POST['new_balance']) ? floatval($_POST['new_balance']) : null;
    if ($new_balance !== null) {
        $stmt = $conn->prepare("
            UPDATE Account
               SET balance = ?
             WHERE account_id = ?
        ");
        $stmt->bind_param("di", $new_balance, $account_id);
        $stmt->execute();
        $stmt->close();
    }
}

if (isset($_POST['suspend'])) {
    $account_id = intval($_POST['account_id']);
    $stmt = $conn->prepare("
        UPDATE Account
           SET account_status = 'suspended'
         WHERE account_id = ?
    ");
    $stmt->bind_param("i", $account_id);
    $stmt->execute();
    $stmt->close();
}

if (isset($_POST['reinstate'])) {
    $account_id = intval($_POST['account_id']);
    $stmt = $conn->prepare("
        UPDATE Account
           SET account_status = 'active'
         WHERE account_id = ?
    ");
    $stmt->bind_param("i", $account_id);
    $stmt->execute();
    $stmt->close();
}

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .card {
            margin: 40px auto;
            padding: 30px;
            max-width: 1000px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        table {
            margin-top: 20px;
        }

        input[type="number"] {
            max-width: 150px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <h1 class="text-center text-primary mb-2">Welcome, <?php echo htmlspecialchars($admin["first_name"]) ?></h1>
            <h5 class="text-center text-muted mb-4">Admin ID:
                <?php echo htmlspecialchars($admin['administrator_id']); ?>
            </h5>

            <form method="post" action="admin_information.php">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped">
                        <thead class="table-primary text-center">
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
                                    <td class="text-center">
                                        <input class="form-check-input" type="radio" name="account_id"
                                            value="<?php echo $row['account_id']; ?>" required>
                                    </td>
                                    <td><?php echo $row['account_id']; ?></td>
                                    <td><?php echo $row['owner_id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                                    <td>$<?php echo number_format($row['balance'], 2); ?></td>
                                    <td>
                                        <span
                                            class="badge <?php echo $row['account_status'] === 'active' ? 'bg-success' : 'bg-secondary'; ?>">
                                            <?php echo htmlspecialchars($row['account_status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <div class="row mt-4 mb-3 align-items-center">
                    <div class="col-md-4">
                        <label for="new_balance" class="form-label">New Balance:</label>
                        <input type="number" step="0.01" name="new_balance" id="new_balance" class="form-control"
                            placeholder="0.00">
                    </div>
                    <div class="col-md-8 d-flex justify-content-end align-items-end gap-2 mt-3 mt-md-0">
                        <button type="submit" name="modify" class="btn btn-warning">Modify Balance</button>
                        <button type="submit" name="suspend" class="btn btn-danger"
                            onclick="return confirm('Suspend this account?');">
                            Suspend
                        </button>
                        <button type="submit" name="reinstate" class="btn btn-success"
                            onclick="return confirm('Reinstate this account?');">
                            Reinstate
                        </button>
                    </div>
                </div>
            </form>

            <div class="text-center mt-4">
                <a href="logout.php" class="btn btn-outline-danger">Logout</a>
            </div>
        </div>
    </div>
</body>

</html>