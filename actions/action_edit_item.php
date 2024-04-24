<?php
declare(strict_types=1);

require_once (__DIR__ . '/../utils/session.php');
$session = new Session();

require_once (__DIR__ . '/../database/connection.db.php');
require_once (__DIR__ . '/../database/item.class.php');

$db = getDatabaseConnection();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
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