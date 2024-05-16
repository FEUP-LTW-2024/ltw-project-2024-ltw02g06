<?php
declare(strict_types=1);

require_once (__DIR__ . '/../utils/session.php');
$session = new Session();
$id = $session->getId();

if (!$id) {
  $redirectUrl = urlencode("/pages/items.php");
  header("Location: login.php?redirect=$redirectUrl");
  exit();
}

require_once (__DIR__ . '/../database/connection.db.php');
require_once (__DIR__ . '/../database/item.class.php');

$db = getDatabaseConnection();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $item_id = filter_var($_POST['item_id'], FILTER_SANITIZE_NUMBER_INT);
  $item_name = filter_var($_POST['item_name'], FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
  $item_description = filter_var($_POST['item_description'], FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
  $item_price = filter_var($_POST['item_price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
  $category = filter_var($_POST['category'], FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
  $images = $_POST['images'];
  $attributes = $_POST['attributes'];

  if (
    empty($item_name) ||
    empty($item_description) ||
    empty($item_price) ||
    empty($category)
  ) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
  }

  // Prepare data for insertion
  $item_data = [
    'id' => $item_id ?? null,
    'name' => $item_name,
    'description' => $item_description,
    'seller' => $id,
    'price' => $item_price,
    'category' => $category,
    'images' => $images ?? [],
    'attributes' => $attributes ?? []
  ];

  Item::updateItem($db, $item_data);
}

if (isset($_GET['redirect'])) {
  header('Location: ' . htmlspecialchars($_GET['redirect'], ENT_QUOTES, 'UTF-8'));
} else {
  header('Location: ' . $_SERVER['HTTP_REFERER']);
}

?>