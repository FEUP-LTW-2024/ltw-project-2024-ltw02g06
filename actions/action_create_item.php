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

  $itemName = filter_var($_POST['itemName'], FILTER_SANITIZE_STRING);
  $itemDescription = filter_var($_POST['itemDescription'], FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
  $itemPrice = filter_var($_POST['itemPrice'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
  $category = filter_var($_POST['category'], FILTER_SANITIZE_NUMBER_INT);
  $images = $_POST['images'];
  $attributes = $_POST['attributes'];

  if (
    empty($itemName) ||
    empty($itemDescription) ||
    empty($itemPrice) ||
    empty($category)
  ) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
  }

  // Prepare data for insertion
  $itemData = [
    'name' => $itemName,
    'description' => $itemDescription,
    'seller' => $id,
    'price' => $itemPrice,
    'category' => $category,
    'images' => $images ?? [],
    'attributes' => $attributes ?? []
  ];

  $item = Item::createItem($db, $itemData);
  header('Location: /pages/item.php?id=' . $item->id);
}

?>