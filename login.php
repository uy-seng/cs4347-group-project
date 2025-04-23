<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
  <link href="styles.css" rel="stylesheet">
</head>

<body>
  <?php
  $errors = array();
  if (isset($_POST["login"])) {
    $email = $_POST["email"];
    $password = $_POST["password"];
    require_once "database.php";
    $sql = "SELECT * FROM Customer WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_array($result, MYSQLI_ASSOC);
    if ($user) {
      $ownerID = $user['customer_id'];
      $sqlAccount = "SELECT * FROM Account WHERE owner_id = $ownerID";
      $resultAccount = mysqli_query($conn, $sqlAccount);
      $account = mysqli_fetch_array($resultAccount, MYSQLI_ASSOC);
      if ($account['account_status'] !== 'active') {
        array_push($errors, "Account Suspended");
      } else if (password_verify($password, $user["password"])) {
        session_start();
        $_SESSION["user"] = $user;
        header("Location: account.php");
        die();
      } else {
        array_push($errors, "Incorrect Password");
      }
    } else {
      $sql = "SELECT * FROM Administrator WHERE Administrator.email = '$email'";
      $result = mysqli_query($conn, $sql);
      $user = mysqli_fetch_array($result, MYSQLI_ASSOC);
      if ($user) {
        if (password_verify($password, $user["password"])) {
          session_start();
          $_SESSION["user"] = $user;
          header("Location: admin_information.php");
          die();
        } else {
          array_push($errors, "Incorrect Password");
        }
      }
      array_push($errors, "Email not found");
    }
  }
  ?>

  <div class="container">
    <h1>Login</h1>
    <?php
    if (count($errors) > 0) {
      foreach ($errors as $error) {
        echo "<div class='alert alert-danger'>$error</div>";
      }
    }
    ?>
    <form action="login.php" method="post">
      <!-- Log In will check user input with customer table-->
      <div class="form-group">
        <label for="email" class="form-label">Email:</label>
        <input type="email" class="form-control" id="email" name="email">
      </div>
      <div class="form-group">
        <label for="password" class="form-label">Password:</label>
        <input type="password" class="form-control" id="password" name="password">
      </div>
      <div class="form-btn form-group">
        <input type="submit" class="btn btn-primary" value="Log In" name="login">
      </div>
    </form>
    <div>
      <p>Not registered yet? <a href="customer_registration.php">Register Here</a></p>
    </div>
  </div>
</body>

</html>