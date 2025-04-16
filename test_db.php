<?php
require 'database.php';  // your connection code
$sql = "
  INSERT INTO Customer
    (first_name, last_name, email, SSN, password)
  VALUES
    ('Test','User','test.user2@example.com','987654321','pass5678')
";
if (mysqli_query($conn, $sql)) {
  echo '✅ Insert succeeded';
} else {
  echo '❌ Insert failed: ' . mysqli_error($conn);
}