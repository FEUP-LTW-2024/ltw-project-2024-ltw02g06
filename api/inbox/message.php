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
    // TODO
    break;
  case 'PATCH':
    // PATCH request handling
    // Update a given item.
    $accepted = json_decode(file_get_contents("php://input"), true)['accepted'];
    $message_id = json_decode(file_get_contents("php://input"), true)['message_id'];
    $user_id = $session->getId();

    if (!$user_id) {
      http_response_code(401); // Unauthorized
      echo json_encode(array("message" => "Not authenticated."));
      exit();
    }

    try {
      $message = Message::getMessage($db, $message_id);

      if ($message->item_seller != $user_id) {
        http_response_code(401); // Unauthorized
        echo json_encode(array("message" => "Not authorized."));
        exit();
      }

      $message = Message::updateMessage($db, $message_id, $accepted);
      if ($message) {
        http_response_code(200); // OK
        echo json_encode($message);
      } else {
        http_response_code(400); // Bad Request
        echo json_encode(array("message" => "Unable to update message."));
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