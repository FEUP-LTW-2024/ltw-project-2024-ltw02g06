<?php
declare(strict_types=1);

require_once (__DIR__ . '/../utils/session.php');
$session = new Session();
$id = $session->getId();

if (!$id) {
  $redirectUrl = urlencode("/pages/item.create.php");
  header("Location: login.php?redirect=$redirectUrl");
  exit();
}

require_once (__DIR__ . '/../database/connection.db.php');
require_once (__DIR__ . '/../database/item.class.php');

$db = getDatabaseConnection();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  if ($session->getSessionToken() !== $_POST['csrf']) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
  }

  $item_name = filter_var($_POST['item_name'], FILTER_SANITIZE_STRING);
  $item_description = filter_var($_POST['item_description'], FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
  $item_price = filter_var($_POST['item_price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
  $category = filter_var($_POST['category'], FILTER_SANITIZE_NUMBER_INT);
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
    'name' => $item_name,
    'description' => $item_description,
    'seller' => $id,
    'price' => $item_price,
    'category' => $category,
    'images' => $images ?? [],
    'attributes' => $attributes ?? []
  ];

  $item = Item::createItem($db, $item_data);
  header('Location: /pages/item.php?id=' . $item->id);
}

?>