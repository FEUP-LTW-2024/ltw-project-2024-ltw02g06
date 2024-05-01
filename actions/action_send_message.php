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
  $message_data = [
    'item_id' => $_POST['item_id'] != '' ? intval($_POST['item_id']) ?? null : null,
    'sender' => $id,
    'receiver' => $_POST['receiver'] != '' ? intval($_POST['receiver']) ?? null : null,
    'value' => $_POST['value'] != '' ? intval($_POST['value']) ?? null : null,
    'message' => $_POST['message'] != '' ? $_POST['message'] : null,
  ];

  if ($message_data['value'] || $message_data['message'])
    Message::sendMessage($db, $message_data);
}

if (isset($_GET['redirect'])) {
  header('Location: ' . $_GET['redirect']);
} else {
  header('Location: ' . $_SERVER['HTTP_REFERER']);
}

?>