<?php
session_start();

require_once 'database.php';

$email    = $_POST['email']    ?? '';
$password = $_POST['password'] ?? '';

function checkTable($mysqli, $table, $email, $password) {
    $idCol = $table === 'Customer' ? 'customer_id' : 'administrator_id';
    $sql   = "SELECT $idCol AS id, password FROM $table WHERE email = ?";
    $stmt  = $mysqli->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 0) {
        $stmt->close();
        return false;       // not found
    }
    $stmt->bind_result($id, $hash);
    $stmt->fetch();
    $stmt->close();

    // verify password
    return password_verify($password, $hash)
         ? $id         
         : null;        
}

$uid = checkTable($mysqli, 'Customer', $email, $password);
if ($uid) {
    $_SESSION['user_type'] = 'customer';
    $_SESSION['user_id']   = $uid;
    echo 'Customer login successful';
    exit;
}
if ($uid === null) {
    echo 'Invalid credentials';
    exit;
}

$aid = checkTable($mysqli, 'Administrator', $email, $password);
if ($aid) {
    $_SESSION['user_type']  = 'administrator';
    $_SESSION['administrator_id'] = $aid;
    echo 'Administrator login successful';
    exit;
}
if ($aid === null) {
    echo 'Invalid credentials';
    exit;
}

echo 'User not found';
exit;
?>
