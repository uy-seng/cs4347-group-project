<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Login</title>
</head>

<body>
  <?php
  if (isset($_POST["login"])) {
    $email = $_POST["email"];
    $password = $_POST["password"];
    require_once "database.php";
    $sql = "SELECT * FROM Customer WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_array($result, MYSQLI_ASSOC);
    if ($user) {
      if (password_verify($password, $user["password"])) {
        session_start();
        $_SESSION["user"] = $user;
        header("Location: account.php");
        die();
      } else {
        echo "<div>Incorrect Password</div>";
      }
    } else {
      $sql = "SELECT * FROM Administrator WHERE Administrator.email = '$email'";
      $result = mysqli_query($conn, $sql);
      $user = mysqli_fetch_array($result, MYSQLI_ASSOC);
      if ($user) {
        if (password_verify($password, $user["password"])) {
          session_start();
          $_SESSION["user"] = $user;
          header("Location: administrator_information.php");
          die();
        } else {
          echo "<div>Incorrect Password</div>";
        }
      }
      echo "<div>Email not found</div>";
    }
  }
  ?>

  <h1>Login</h1>
  <form action="login.php" method="post">
    <!-- Log In will check user input with customer table-->
    <label for="email">Email:</label>
    <input type="email" id="email" name="email">
    <br><br>
    <label for="password">Password:</label>
    <input type="password" id="password" name="password">
    <br><br>
    <input type="submit" value="Log In" name="login">
  </form>
  <div>
    <p>Not registered yet <a href="customer_registration.php">Register Here</a></p>
  </div>
</body>

</html>