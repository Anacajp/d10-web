<?php
header("Content-Type: application/json");
echo json_encode(["name" => $_GET["name"] ?? ""]);
?>
