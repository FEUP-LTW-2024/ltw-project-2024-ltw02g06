<?php
declare(strict_types=1);

require_once (__DIR__ . '/../utils/session.php');
$session = new Session();
$id = $session->getId();

if (!$id) {
  header("Location: login.php");
  exit();
}

require_once (__DIR__ . '/../database/connection.db.php');
require_once (__DIR__ . '/../database/message.class.php');

$db = getDatabaseConnection();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  if ($session->getSessionToken() !== $_POST['csrf']) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
  }

  $itemId = isset($_POST['itemId']) ? filter_var($_POST['itemId'], FILTER_VALIDATE_INT) : null;
  $receiver = isset($_POST['receiver']) ? filter_var($_POST['receiver'], FILTER_VALIDATE_INT) : null;
  $value = isset($_POST['value']) ? filter_var($_POST['value'], FILTER_SANITIZE_NUMBER_FLOAT) : null;
  $value = $value != "" ? $value : null;
  $message = isset($_POST['message']) ? htmlspecialchars($_POST['message'], ENT_QUOTES, 'UTF-8') : null;

  $messageData = [
    'itemId' => $itemId,
    'sender' => $id,
    'receiver' => $receiver,
    'value' => $value,
    'message' => $message,
  ];

  if ($value !== null || $message !== null) {
    Message::sendMessage($db, $messageData);
  }
}

if (isset($_GET['redirect'])) {
  header('Location: ' . htmlspecialchars($_GET['redirect'], ENT_QUOTES, 'UTF-8'));
} else {
  header('Location: ' . $_SERVER['HTTP_REFERER']);
}

?>