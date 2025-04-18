<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Customer Form</title>
</head>

<body>
  <?php
  if (isset($_POST["submit"])) {
    $firstName = $_POST["first_name"];
    $lastName = $_POST["last_name"];
    $email = $_POST["email"];
    $SSN = $_POST["SSN"];
    $password = $_POST["password"];
    $passwordRepeat = $_POST["password_repeat"];

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $errors = array();

    if (empty($firstName) or empty($lastName) or empty($email) or empty($SSN) or empty($password)) {
      array_push($errors, "All fields are required");
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      array_push($errors, "Email is not valid.");
    }
    if (strlen($SSN) !== 9) {
      array_push($errors, "SSN must be 9 digits.");
    }
    if (strlen($password) > 100) {
      array_push($errors, "Password must be less than 100 characters.");
    }
    if ($password !== $passwordRepeat) {
      array_push($errors, "Passwords do not match.");
    }

    if (count($errors) > 0) {
      foreach ($errors as $error) {
        echo "<div>$error</div>";
      }
    } else {
      require_once "database.php";
      mysqli_begin_transaction($conn);
      $sql = "INSERT INTO customer (first_name, last_name, email, SSN, password) VALUES (?,?,?,?,?)";
      $stmt = mysqli_stmt_init($conn);
      $prepareStmt = mysqli_stmt_prepare($stmt, $sql);
      if ($prepareStmt) {
        mysqli_stmt_bind_param($stmt, "sssss", $firstName, $lastName, $email, $SSN, $passwordHash);
        mysqli_stmt_execute($stmt);

        $customerID = mysqli_insert_id($conn);
        $accountStatus = "active";
        $balance = 0.00;
        $accountHistory = "";

        $sqlAccount = "INSERT INTO account (owner_id, account_status, balance, account_history) VALUES (?,?,?,?)";
        $stmtAccount = mysqli_stmt_init($conn);
        $prepareStmtAccount = mysqli_stmt_prepare($stmtAccount, $sqlAccount);
        if ($prepareStmtAccount) {
          mysqli_stmt_bind_param($stmtAccount, "isds", $customerID, $accountStatus, $balance, $accountHistory);
          mysqli_stmt_execute($stmtAccount);
          mysqli_commit($conn);
        } else {
          die("Something went wrong");
        }

        echo "<div>Registration Successful! An account has been opened for you.</div>";
      } else {
        die("Something went wrong");
      }
    }
  }
  ?>

  <h1>Customer Registration Form</h1>
  <form action="customer_registration.php" method="post">
    <!-- Note: customer_id is auto-generated so it isn’t required as user input -->
    <label for="first_name">First Name:</label>
    <input type="text" id="first_name" name="first_name">
    <br><br>
    <label for="last_name">Last Name:</label>
    <input type="text" id="last_name" name="last_name">
    <br><br>
    <label for="email">Email:</label>
    <input type="email" id="email" name="email">
    <br><br>
    <label for="SSN">SSN:</label>
    <input type="text" id="SSN" name="SSN">
    <br><br>
    <label for="password">Password:</label>
    <input type="password" id="password" name="password">
    <br><br>
    <label for="password">Repeat Password:</label>
    <input type="password" id="password" name="password_repeat">
    <br><br>
    <input type="submit" value="Register" name="submit">
  </form>
  <div>
    <p>Already Registered <a href="login.php">Login Here</a></p>
  </div>
</body>

</html>