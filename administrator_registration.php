<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>Administrator Form</title>
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
      $sql = "INSERT INTO administrator (first_name, last_name, email, SSN, password) VALUES (?,?,?,?,?)";
      $stmt = mysqli_stmt_init($conn);
      $prepareStmt = mysqli_stmt_prepare($stmt, $sql);
      if ($prepareStmt) {
        mysqli_stmt_bind_param($stmt, "sssss", $firstName, $lastName, $email, $SSN, $passwordHash);
        mysqli_stmt_execute($stmt);
        echo "<div>Registration Successful!</div>";
      } else {
        die("Something went wrong");
      }
    }
  }
  ?>
  <h1>Administrator Registration Form</h1>
  <form action="administrator_registration.php" method="post">
    <!-- Note: administrator_id & customer_id is auto-generated so it isn’t needed as input -->
    <label for="first_name">First Name:</label>
    <input type="text" id="first_name" name="first_name" />
    <br /><br />
    <label for="last_name">Last Name:</label>
    <input type="text" id="last_name" name="last_name" />
    <br /><br />
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" />
    <br /><br />
    <label for="SSN">SSN:</label>
    <input type="text" id="SSN" name="SSN" />
    <br /><br />
    <label for="password">Password:</label>
    <input type="password" id="password" name="password" />
    <br /><br />
    <label for="password">Repeat Password:</label>
    <input type="password" id="password" name="password_repeat">
    <br><br>
    <input type="submit" value="Register" name="submit">
  </form>
</body>

</html>