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
  if (
    empty($_POST['item_name']) ||
    empty($_POST['item_description']) ||
    empty($_POST['item_price']) ||
    empty($_POST['category'])
  ) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
  }

  $item_data = [
    'id' => $_POST['item_id'] ?? null,
    'name' => $_POST['item_name'] ?? '',
    'price' => $_POST['item_price'] ?? '',
    'description' => $_POST['item_description'] ?? '',
    'category' => $_POST['category'] ?? '',
    'images' => $_POST['images'] ?? [],
    'attributes' => $_POST['attributes'] ?? []
  ];

  Item::updateItem($db, $item_data);
}

if (isset($_GET['redirect'])) {
  header('Location: ' . $_GET['redirect']);
} else {
  header('Location: ' . $_SERVER['HTTP_REFERER']);
}

?>