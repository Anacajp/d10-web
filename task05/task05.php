<?php
header("Content-Type: application/json");
$type = trim($_GET["type"] ?? "");
$brand = trim($_GET["brand"] ?? "");
$price = trim($_GET["price"] ?? "");
$numberInput = $_GET["number"] ?? "";
$number = filter_var($numberInput, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
if (strlen($type) < 3) {
  respond_with_error("Error ($type): this type does not have enough characters.");
}
if (strlen($type) > 10) {
  respond_with_error("Error ($type): this type has too many characters.");
}
if (!preg_match("/^[a-z-]+$/i", $type)) {
  respond_with_error("Error ($type): this type has non-alphabetical characters (different from '-').");
}
if (strlen($brand) < 2) {
  respond_with_error("Error ($brand): this brand does not have enough characters.");
}
if (strlen($brand) > 20) {
  respond_with_error("Error ($brand): this brand has too much characters.");
}
if (!preg_match("/^[a-z0-9&-]+$/i", $brand)) {
  respond_with_error("Error ($brand): this brand has invalid characters.");
}
if (strlen($price) < 2) {
  respond_with_error("Error ($price): this price does not have enough characters.");
}
if (strlen($price) > 5) {
  respond_with_error("Error ($price): this price has too many characters.");
}
if (!preg_match("/^[<>=][0-9]+$/", $price)) {
  respond_with_error("Error ($price): we cannot define a price - string invalid.");
}
if ($number === false) {
  respond_with_error("Error ($numberInput): not a positive number.");
}
preg_match("/^([<>=])([0-9]+)$/", $price, $parts);
$operator = $parts[1];
$priceValue = (int) $parts[2];
try {
  $db = new PDO("sqlite:" . __DIR__ . "/../day10_material/ajax_products.db");
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $findType = $db->prepare("SELECT COUNT(*) FROM products WHERE lower(type) = lower(?)");
  $findType->execute([$type]);
  if (!$findType->fetchColumn()) {
    respond_with_error("Error ($type): this type doesn't exist in our shop.");
  }
  $findBrand = $db->prepare("SELECT COUNT(*) FROM products WHERE lower(type) = lower(?) AND lower(brand) = lower(?)");
  $findBrand->execute([$type, $brand]);
  if (!$findBrand->fetchColumn()) {
    respond_with_error("Error ($brand): this brand doesn't exist in our database.");
  }
  $query = "SELECT type, brand, price, ? AS number, stock FROM products " .
  "WHERE lower(type) = lower(?) AND lower(brand) = lower(?) AND price " . $operator . " ?";
  $statement = $db->prepare($query);
  $statement->execute([$number, $type, $brand, $priceValue]);
  $products = $statement->fetchAll(PDO::FETCH_ASSOC);
  if (!$products) {
    respond_with_error("Error ($price): no products found at this price.");
  }
  foreach ($products as $product) {
    if ($number > $product["stock"]) {
      respond_with_error("Error ($number): sorry, we don't have enough stock, we only have " . $product["stock"] . " in stock.");
    }
  }
  echo json_encode(["products" => $products]);
}
catch (Throwable $error) {
  http_response_code(500);
  echo json_encode(["error" => "Database is not available."]);
}
function respond_with_error(string $message): void {
  http_response_code(400);
  echo json_encode(["error" => $message]);
  exit;
}
?>
