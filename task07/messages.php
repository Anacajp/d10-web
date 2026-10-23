<?php
header("Content-Type: application/json");
$file = __DIR__ . "/messages.json";
$messages = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $name = trim($_POST["name"] ?? "");
  $message = trim($_POST["message"] ?? "");
  if ($name !== "" && $message !== "") {
    $messages[] = ["name"=>$name, "message"=>$message];
    file_put_contents($file, json_encode($messages), LOCK_EX);
  }
}
echo json_encode($messages);
?>
