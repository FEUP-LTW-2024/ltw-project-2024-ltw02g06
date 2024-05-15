<?php
declare(strict_types=1);

require_once (__DIR__ . '/../utils/session.php');
$session = new Session();
$id = $session->getId();

if (!$id) {
  $redirectUrl = urlencode("/pages/profile.php");
  header("Location: login.php?redirect=$redirectUrl");
  exit();
}

require_once (__DIR__ . '/../database/connection.db.php');
require_once (__DIR__ . '/../database/user.class.php');

$db = getDatabaseConnection();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  if (
    empty($_POST['first_name']) ||
    empty($_POST['last_name']) ||
    empty($_POST['email']) ||
    empty($_POST['address']) ||
    empty($_POST['zipcode']) ||
    empty($_POST['city']) ||
    empty($_POST['state']) ||
    empty($_POST['country'])
  ) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
  }

  $user_data = [
    'id' => $id,
    'first_name' => $_POST['first_name'] ? $_POST['first_name'] != "" ? $_POST['first_name'] : null : null,
    'last_name' => $_POST['last_name'] ? $_POST['last_name'] != "" ? $_POST['last_name'] : null : null,
    'email' => $_POST['email'] ? $_POST['email'] != "" ? $_POST['email'] : null : null,
    'new_image' => $_POST['new_image_path'] ? $_POST['new_image_path'] != "" ? $_POST['new_image_path'] : null : null,
    'address' => $_POST['address'] ? $_POST['address'] != "" ? $_POST['address'] : null : null,
    'zipcode' => $_POST['zipcode'] ? $_POST['zipcode'] != "" ? $_POST['zipcode'] : null : null,
    'city' => $_POST['city'] ? $_POST['city'] != "" ? $_POST['city'] : null : null,
    'state' => $_POST['state'] ? $_POST['state'] != "" ? $_POST['state'] : null : null,
    'country' => $_POST['country'] ? $_POST['country'] != "" ? $_POST['country'] : null : null,
  ];

  User::updateUser($db, $user_data);
}

if (isset($_GET['redirect'])) {
  header('Location: ' . $_GET['redirect']);
} else {
  header('Location: ' . $_SERVER['HTTP_REFERER']);
}

?>