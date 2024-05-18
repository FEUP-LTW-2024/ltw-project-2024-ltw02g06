<?php
declare(strict_types=1);

require_once (__DIR__ . '/../../utils/session.php');
$session = new Session();

require_once (__DIR__ . '/../../database/connection.db.php');
require_once (__DIR__ . '/../../database/message.class.php');

$db = getDatabaseConnection();

$request_method = $_SERVER['REQUEST_METHOD'];

switch ($request_method) {
  case 'GET':
    // GET request handling
    $user_id = $session->getId();

    if ($user_id === null) {
      http_response_code(401); // Unauthorized
      echo json_encode(array("message" => "Not authenticated."));
      exit();
    }

    if ($session->getSessionToken() !== $_GET['csrf']) {
      http_response_code(401); // Unauthorized
      echo json_encode(array("message" => "Unauthorized."));
      exit();
    }

    // Extract and sanitize parameters from the URL
    $item_id = isset($_GET['item']) ? filter_var($_GET['item'], FILTER_VALIDATE_INT) : null;
    $other_user = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : null;

    try {
      $chat = Message::getChat($db, $item_id, $user_id, $other_user);
      if ($chat) {
        http_response_code(200); // OK
        echo json_encode($chat);
      } else {
        http_response_code(404); // Not Found
        echo json_encode(array("message" => "No messages found."));
      }
    } catch (PDOException $e) {
      http_response_code(500); // Internal Server Error
      echo json_encode(array("message" => $e->getMessage()));
    }
    break;
  default:
    // Handle unsupported request methods
    http_response_code(405);
    exit("Unsupported request method");
}
?>