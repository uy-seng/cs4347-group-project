<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>Administrator Form</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
  <link href="styles.css" rel="stylesheet">
</head>

<body>
  <?php
  $errors = array();
  if (isset($_POST["submit"])) {
    $firstName = $_POST["first_name"];
    $lastName = $_POST["last_name"];
    $email = $_POST["email"];
    $SSN = $_POST["SSN"];
    $password = $_POST["password"];
    $passwordRepeat = $_POST["password_repeat"];
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $completeTransaction = "";
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
    require_once "database.php";
    // check if email exist in administrator table
    $checkSql = "SELECT email FROM Administrator WHERE email = ?";
    $chkStmt = mysqli_prepare($conn, $checkSql);
    mysqli_stmt_bind_param($chkStmt, "s", $email);
    mysqli_stmt_execute($chkStmt);
    mysqli_stmt_store_result($chkStmt);

    // if email exist we will not allow registration
    if (mysqli_stmt_num_rows($chkStmt) > 0) {
      array_push($errors, "This email is already registered.");
    }
    mysqli_stmt_close($chkStmt);

    // check if email exist in customer table
    $checkSql = "SELECT email FROM Customer WHERE email = ?";
    $chkStmt = mysqli_prepare($conn, $checkSql);
    mysqli_stmt_bind_param($chkStmt, "s", $email);
    mysqli_stmt_execute($chkStmt);
    mysqli_stmt_store_result($chkStmt);

    // if email exist we will not allow registration
    if (mysqli_stmt_num_rows($chkStmt) > 0) {
      array_push($errors, "This email is already registered.");
    }
    mysqli_stmt_close($chkStmt);
    if (count($errors) === 0) {
      $sql = "INSERT INTO Administrator (first_name, last_name, email, SSN, password) VALUES (?,?,?,?,?)";
      $stmt = mysqli_stmt_init($conn);
      $prepareStmt = mysqli_stmt_prepare($stmt, $sql);
      if ($prepareStmt) {
        mysqli_stmt_bind_param($stmt, "sssss", $firstName, $lastName, $email, $SSN, $passwordHash);
        mysqli_stmt_execute($stmt);
        $completeTransaction = "Registration Successful!";
      } else {
        die("Something went wrong");
      }
    }
  }
  ?>

  <div class="container">
    <h1>Administrator Registration</h1>
    <?php
    if (count($errors) > 0) {
      foreach ($errors as $error) {
        echo "<div class='alert alert-danger'>$error</div>";
      }
    }

    if (!empty($completeTransaction)) {
      echo "<div class='alert alert-success'>$completeTransaction</div>";
    }
    ?>
    <form action="administrator_registration.php" method="post" class="mb-4">
      <div class="form-group">
        <label for="first_name" class="form-label">First Name:</label>
        <input type="text" class="form-control" id="first_name" name="first_name" />
      </div>
      <div class="form-group">
        <label for="last_name" class="form-label">Last Name:</label>
        <input type="text" class="form-control" id="last_name" name="last_name" />
      </div>
      <div class="form-group">
        <label for="email" class="form-label">Email:</label>
        <input type="email" class="form-control" id="email" name="email" />
      </div>
      <div class="form-group">
        <label for="SSN" class="form-label">SSN:</label>
        <input type="text" class="form-control" id="SSN" name="SSN" />
      </div>
      <div class="form-group">
        <label for="password" class="form-label">Password:</label>
        <input type="password" class="form-control" id="password" name="password" />
      </div>
      <div class="form-group">
        <label for="password_repeat" class="form-label">Repeat Password:</label>
        <input type="password" class="form-control" id="password_repeat" name="password_repeat" />
      </div>
      <div class="form-group">
        <input type="submit" class="btn btn-primary" value="Register" name="submit">
      </div>
    </form>
    <div>
      <p>Already Registered <a href="login.php">Login Here</a></p>
    </div>
  </div>
</body>

</html>