<?php 
session_start();

include "functions.php";

if(!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] !== true) {
  session_destroy();
  header("Location: login.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home</title>
</head>
<body>

  <h2>HomePage</h2>

  <p>Welcome <?php echo htmlspecialchars(trim(($_SESSION["username"]))); ?></p>
  <br>
  <a href="logout.php">Log out</a>

</body>
</html>