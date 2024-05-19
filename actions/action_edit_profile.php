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
  if ($session->getSessionToken() !== $_POST['csrf']) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
  }

  $firstName = filter_var($_POST['firstName'], FILTER_SANITIZE_STRING);
  $lastName = filter_var($_POST['lastName'], FILTER_SANITIZE_STRING);
  $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
  $newImagePath = filter_var($_POST['newImagePath'], FILTER_SANITIZE_STRING);
  $address = filter_var($_POST['address'], FILTER_SANITIZE_STRING);
  $zipcode = filter_var($_POST['zipcode'], FILTER_SANITIZE_STRING);
  $city = filter_var($_POST['city'], FILTER_SANITIZE_STRING);
  $state = filter_var($_POST['state'], FILTER_SANITIZE_STRING);
  $country = filter_var($_POST['country'], FILTER_SANITIZE_STRING);

  if (
    empty($firstName) ||
    empty($lastName) ||
    empty($email) ||
    empty($address) ||
    empty($zipcode) ||
    empty($city) ||
    empty($state) ||
    empty($country)
  ) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
  }

  $userData = [
    'id' => $id,
    'firstName' => $firstName,
    'lastName' => $lastName,
    'email' => $email,
    'newImage' => $newImagePath,
    'address' => $address,
    'zipcode' => $zipcode,
    'city' => $city,
    'state' => $state,
    'country' => $country,
  ];

  User::updateUser($db, $userData);
}

if (isset($_GET['redirect'])) {
  header('Location: ' . htmlspecialchars($_GET['redirect'], ENT_QUOTES, 'UTF-8'));
} else {
  header('Location: ' . $_SERVER['HTTP_REFERER']);
}

?>