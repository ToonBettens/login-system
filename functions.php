<?php 
/**
 * @pre $data instanceof string
 * @post result = htmlspecialchars(trim($data))
 */
function validate_input($data) {
  $data = trim($data);
  $data = htmlspecialchars($data);
  return $data;
}
?>