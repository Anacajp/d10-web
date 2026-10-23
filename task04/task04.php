<?php
header("Content-Type: application/json");
$type = trim($_GET["type"] ?? "");
$brand = trim($_GET["brand"] ?? "");
$typeValid = false;
$brandValid = false;
if ($type === "") {
  $typeMessage = "No type sent yet!";
}
else if (strlen($type) < 3) {
  $typeMessage = "$type: this type does not have enough characters.";
}
else if (strlen($type) > 10) {
  $typeMessage = "$type: this type has too many characters.";
}
else if (!preg_match("/^[a-z-]+$/i", $type)) {
  $typeMessage = "$type: this type has non-alphabetical characters (different from '-').";
}
else {
  $typeMessage = "";
  $typeValid = true;
}
if ($brand === "") {
  $brandMessage = "No brand sent yet!";
}
else if (strlen($brand) < 2) {
  $brandMessage = "$brand: this brand does not have enough characters.";
}
else if (strlen($brand) > 20) {
  $brandMessage = "$brand: this brand has too many characters.";
}
else if (!preg_match("/^[a-z0-9&-]+$/i", $brand)) {
  $brandMessage = "$brand: this brand has invalid characters.";
}
else {
  $brandMessage = "";
  $brandValid = true;
}
if ($typeValid || $brandValid) {
  try {
    $db = new PDO("sqlite:" . __DIR__ . "/../day10_material/ajax_products.db");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if ($typeValid) {
      $findType = $db->prepare("SELECT COUNT(*) FROM products WHERE lower(type) = lower(?)");
      $findType->execute([$type]);
      if (!$findType->fetchColumn()) {
        $typeValid = false;
        $typeMessage = "$type: this type doesn't exist in our shop.";
      }
      else {
        $typeMessage = "$type: this type exists in databases.";
      }
    }
    if ($brandValid) {
      if (!$typeValid) {
        $brandValid = false;
        $brandMessage = $type === "" ? "No type sent yet!" : "$brand: this brand cannot be checked until the type is valid.";
      }
      else {
        $findBrand = $db->prepare("SELECT COUNT(*) FROM products WHERE lower(type) = lower(?) AND lower(brand) = lower(?)");
        $findBrand->execute([$type, $brand]);
        if ($findBrand->fetchColumn()) {
          $brandValid = false;
          $brandMessage = "$brand: this brand already exists in databases.";
        }
        else {
          $brandMessage = "$brand: this brand is valid for the type $type.";
        }
      }
    }
  }
  catch (Throwable $error) {
    $typeValid = false;
    $brandValid = false;
    $typeMessage = "Database is not available.";
    $brandMessage = "Database is not available.";
  }
}
echo json_encode([ "typeMessage" => $typeMessage, "brandMessage" => $brandMessage, "typeValid" => $typeValid, "brandValid" => $brandValid ]);
?>
