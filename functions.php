<?php 
/**
 * @post result = htmlspecialchars(trim($data))
 */
function validate_input(string $data) {
  $data = trim($data);
  $data = htmlspecialchars($data);
  return $data;
}


function validate_password(string $password, string $salt, string $hashed_password, string $keyspace = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ") {

  $password = add_salt($password, $salt);
  for($i = 0; $i < 62; ++$i) {
    $data = add_pepper($password, $keyspace[$i]);
    $data = hash("sha512", $data);
    if($data == $hashed_password) {
      return true;
    }
  }
  return false;
}


function generate_password(string $data){

  $salt = random_str(8);
  $data = add_salt($data, $salt);
  $data = add_pepper($data, random_str(1));
  $data = hash('sha512', $data);

  return array("data" => $data, "salt" => $salt);
}


/**
 * @throws IllegalArgumentException strlen($salt) < 1;
 */
function add_salt(string $data, string $salt) {

  $min_length = min(strlen($data), strlen($salt)) - 1;
  for($i = 0; $i < $min_length; ++$i) {

    $place = $i*2 + 1;
    $data = substr_replace($data, $salt[$i], $place, 0);
  }

  return $data;
}


function add_pepper(string $data, string $pepper) {

  $length = strlen($data);
  $data = substr_replace($data, $pepper, ceil($length/4), 0);

  return $data;
}


/**
 * @throws RangeException $length < 1
 */
function random_str(int $length = 8, string $keyspace = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ") {

  if ($length < 1) {
    throw new \RangeException("Length must be a positive integer");
  }

  $pieces = [];
  $max = strlen($keyspace) - 1;
  for($i = 0; $i < $length; ++$i) {
    $pieces []= $keyspace[random_int(0, $max)];
  }
  return implode("", $pieces);
}
?>