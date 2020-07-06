<?php
define("DB_SERVER", 'localhost');
define("DB_USERNAME", "admin");
define("DB_PASSWORD", "adminpwd");
define("DB_NAME", "login-system");

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if($conn->connect_error) {
  die("connection failed: " . $conn->connect_error);
}
?>