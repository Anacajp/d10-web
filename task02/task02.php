<?php
header("Content-Type: application/json");
$email = $_POST["email"] ?? "";
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  http_response_code(400);
  echo json_encode(["valid" => false]);
  exit;
}
echo json_encode(["valid" => true]);
?>
