<?php
declare(strict_types=1);

require_once (__DIR__ . '/../utils/session.php');
$session = new Session();

require_once (__DIR__ . '/../database/connection.db.php');
require_once (__DIR__ . '/../database/user.class.php');

$db = getDatabaseConnection();

$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$password = $_POST['password'];
$user = User::getUserWithPassword($db, $email, $password);

if ($user) {
  $session->setId($user->id);
  $session->setName($user->name());
  $session->addMessage('success', 'Login successful!');
  if (isset($_GET['redirect'])) {
    header('Location: ' . htmlspecialchars($_GET['redirect'], ENT_QUOTES, 'UTF-8'));
  } else {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
  }
} else {
  $session->addMessage('error', 'Wrong credentials!');
}

?>