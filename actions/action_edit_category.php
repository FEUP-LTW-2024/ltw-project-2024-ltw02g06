<?php
declare(strict_types=1);

require_once (__DIR__ . '/../utils/session.php');
$session = new Session();
$id = $session->getId();

if (!$id) {
  $redirectUrl = urlencode("/pages/admin.php");
  header("Location: ../pages/login.php?redirect=$redirectUrl");
  exit();
}

require_once (__DIR__ . '/../database/connection.db.php');
require_once (__DIR__ . '/../database/user.class.php');
require_once (__DIR__ . '/../database/item.class.php');

$db = getDatabaseConnection();

$user = User::getUser($db, $id);

if (!$user->admin) {
  header("Location: ../pages/admin.php");
  exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $category_data = [
    'id' => $_POST['category'] ?? null,
    'attributes' => $_POST['attribute'] ?? [],
  ];

  Category::updateCategory($db, $category_data);
}

if (isset($_GET['redirect'])) {
  header('Location: ' . $_GET['redirect']);
} else {
  header('Location: ' . $_SERVER['HTTP_REFERER']);
}

?>