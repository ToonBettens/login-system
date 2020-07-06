<?php

include "functions.php";
require_once "config.php";

$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {

  //username validation
  $username = validate_input($_POST["username"]);

  if(empty($username)) {
    $username_err = "Enter a username.";
  
  }elseif(!preg_match("/^[a-zA-z\d_]*$/", $username)){
    $username_err = "Only letters, digits and underscores are allowed.";
  
  }else{ 

    //uniqueness check
    if($stmt = $conn->prepare("SELECT U.userID FROM USER as U WHERE U.username = ?")) {

      $stmt->bind_param("s", $username);

      if($stmt->execute()) {

        $stmt->store_result();

        if($stmt->num_rows > 0) {
          $username_err = "This username is already taken.";
        }
      }else{
        echo "Something went wrong. Please try again later.";
      }
    }else{
      echo "Something went wrong. Please try again later.";
    }
    $stmt->close();
  }

  //password validation
  $password = validate_input($_POST["password"]);

  if(empty($password)) {
    $password_err = "Enter a password.";
  } elseif(strlen($password) < 8) {
    $password_err = "Password must have atleast 8 characters.";
  } elseif(strpos($password, " ")) {
    $password_err = "No whitespaces are allowed";
  }

  //confirm_password validation
  $confirm_password = validate_input($_POST["confirm_password"]);

  if(empty($confirm_password)) {
    $confirm_password_err = "Confirm your password.";
  } elseif($password != $confirm_password) {
    $confirm_password_err = "Password did not match.";
  } 
  
  //insert new user (if no error occured)
  if(empty($username_err) && empty($password_err) && empty($confirm_password_err)) {

    if($stmt = $conn->prepare("INSERT INTO USER (username, password, salt) VALUES (?, ?, ?)")) {

      $pwd_arr = generate_password($password);
      $stmt->bind_param("sss", $username, $pwd_arr["data"], $pwd_arr["salt"]);

      if($stmt->execute()) {

        session_start();
        $_SESSION["logged_in"] = true;
        $_SESSION["username"] = $username;
        header("Location: fetch_id.php");
        exit;

      }else{
        echo "Something went wrong. Please try again later.";
      }
      $stmt->close();
    }
  }
}

  $conn->close();
?>

<!DOCTYPE html>
<html lang="en-us">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register form</title>
</head>

<body>

  <h2>Register Form</h2>

  <span>* required field</span>

  <br><br>

  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

    <label for="username">Username:</label>
    <input type="text" id="username" name="username" value="<?php echo $username; ?>">
    <span>* <?php echo $username_err; ?></span>

    <br><br>

    <label for="password">Password:</label>
    <input type="text" id="password" name="password" value="<?php echo $password; ?>">
    <span>* <?php echo $password_err; ?></span>
    
    <br><br>

    <label for="confirm_password">Confirm password:</label>
    <input type="text" id="confirm_password" name="confirm_password" value="<?php echo $confirm_password; ?>">
    <span>* <?php echo $confirm_password_err; ?></span>

    <br><br>

    <input type="submit" value="Submit">

  </form>

  <br><br>

  <a href="login.php">Login</a>

</body>

</html>