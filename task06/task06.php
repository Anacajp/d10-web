<?php
header("Content-Type: application/json");
$type = trim($_POST["type"] ?? "");
$brand = trim($_POST["brand"] ?? "");
$price = filter_var($_POST["price"] ?? null, FILTER_VALIDATE_INT, ["options" => ["min_range" => 0]]);
$stock = filter_var($_POST["number"] ?? null, FILTER_VALIDATE_INT, ["options" => ["min_range" => 0]]);
if (!preg_match("/^[a-z-]{3,10}$/i", $type) || !preg_match("/^[a-z0-9&-]{2,20}$/i", $brand) || $price === false || $stock === false) {
  http_response_code(400);
  echo json_encode(["error"=>"Please complete all fields with valid values."]);
  exit;
}
try {
  $db = new PDO("sqlite:" . __DIR__ . "/../day10_material/ajax_products.db");
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $findType = $db->prepare("SELECT COUNT(*) FROM products WHERE lower(type)=lower(?)");
  $findType->execute([$type]);
  if (!$findType->fetchColumn()) {
    http_response_code(400);
    echo json_encode(["error"=>"This type does not exist in our shop."]);
    exit;
  }
  $find = $db->prepare("SELECT id FROM products WHERE lower(type)=lower(?) AND lower(brand)=lower(?)");
  $find->execute([$type, $brand]);
  $id = $find->fetchColumn();
  if ($id !== false) {
    $save = $db->prepare("UPDATE products SET price=?, stock=? WHERE id=?");
    $save->execute([$price, $stock, $id]);
    $message = "Product updated.";
  }
  else {
    $save = $db->prepare("INSERT INTO products (type, brand, price, stock) VALUES (?, ?, ?, ?)");
    $save->execute([$type, $brand, $price, $stock]);
    $message = "Product added.";
  }
  echo json_encode(["message"=>$message]);
}
catch (Throwable $error) {
  http_response_code(500);
  echo json_encode(["error"=>"Database is not available."]);
}
?>
