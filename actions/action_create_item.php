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
  $item_data = [
    'name' => $_POST['item_name'] ?? '',
    'description' => $_POST['item_description'] ?? '',
    'seller' => $id,
    'price' => $_POST['item_price'] ?? 0,
    'category' => $_POST['category'] ?? '',
    'images' => $_POST['images'] ?? [],
    'attributes' => $_POST['attributes'] ?? []
  ];

  $item = Item::createItem($db, $item_data);
  header('Location: /pages/item.php?id=' . $item->id);
}

?>