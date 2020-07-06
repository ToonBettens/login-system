<?php 
session_start();

include "functions.php";

if(!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] !== true) {
  header("Location: login.php?err=1");
  exit;
}

if(isset($_SESSION["username"]) && isset($_SESSION["id"])) {
  header("Location: home.php");
  exit;
}

require_once "config.php";

if($stmt = $conn->prepare("SELECT U.userID FROM USER as U WHERE U.username = ?")) {

  $stmt->bind_param("s", $username);

  $username = validate_input($_SESSION["username"]);

  if($stmt->execute()) {

    $stmt->store_result();

    if($stmt->num_rows == 1) {
      $stmt->bind_result($id);

      if($stmt->fetch()) {
        $_SESSION["id"] = $id;
        header("Location: home.php?");
        exit;

      }else{
        header("Location: login.php?err=1");
      exit;
      }
    }else{
      header("Location: login.php?err=1");
    }
  }else{
    header("Location: login.php?err=1");
    exit;
  }
}else{
  header("Location: login.php?err=1");
  exit;
}
$stmt->close();
$conn->close();
?>