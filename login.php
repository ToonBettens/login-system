<?php
session_start();

if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === true && isset($_SESSION["username"]) && isset($_SESSION["id"])) {
  header("Location: home.php");
  exit;
}

include "functions.php";
require_once "config.php";

$username = $password = "";
$username_err = $password_err = "";
$error_notice = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  //username validation
  $username = validate_input($_POST["username"]);

  if (empty($username)) {
    $username_err = "Enter your username.";
  }

  //password validation
  $password = validate_input($_POST["password"]);

  if (empty($password)) {
    $password_err = "Enter your password.";
  }

  //check user credentials
  if (empty($username_err) && empty($password_err)) {

    if ($stmt = $conn->prepare(
      "SELECT U.userID, U.password, U.salt FROM USER as U WHERE U.username = ?"
    )) {

      $stmt->bind_param("s", $username);

      if ($stmt->execute()) {

        $stmt->store_result();

        if ($stmt->num_rows == 1) {
          $stmt->bind_result($id, $hashed_password, $salt);

          if ($stmt->fetch()) {
            if (validate_password($password, $salt, $hashed_password)) {
              session_start();
              $_SESSION["logged_in"] = true;
              $_SESSION["id"] = $id;
              $_SESSION["username"] = $username;
              header("Location: home.php");
              exit;
            } else {
              $password_err = "Given password does not match the username";
            }
          } else {
            echo "Something went wrong. Please try again later.";
          }
        } else {
          $username_err = "No account registered to this username. Register to create account.";
        }
      } else {
        echo "Something went wrong. Please try again later.";
      }
    } else {
      echo "Something went wrong. Please try again later.";
    }
    $stmt->close();
  }
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["err"]) && $_GET["err"] == 1) {
  $error_notice = "Oops, something went wrong. Please try to login again.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Form</title>
</head>

<body>

  <h2>Login Form</h2>

  <?php
  if (!empty($error_notice)) {
    echo "<span>$error_notice</span><br><br>";
  }
  ?>

  <span>Enter your username and password</span>

  <br><br>

  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

    <label for="username">Username:</label>
    <input type="text" id="username" name="username" value="<?php echo $username; ?>">
    <span>&ensp; <?php echo $username_err; ?></span>

    <br><br>

    <label for="password">Password:</label>
    <input type="text" id="password" name="password" value="<?php echo $password; ?>">
    <span>&ensp; <?php echo $password_err; ?></span>

    <br><br>

    <input type="submit" value="Submit">

  </form>

  <br><br>

  <a href="register.php">Register</a>
</body>

</html>