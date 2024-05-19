<?php
declare(strict_types=1);

require_once (__DIR__ . '/../../utils/session.php');
$session = new Session();

require_once (__DIR__ . '/../../database/connection.db.php');
require_once (__DIR__ . '/../../database/message.class.php');

$db = getDatabaseConnection();

$request_method = $_SERVER['REQUEST_METHOD'];

switch ($request_method) {
  case 'PATCH':
    // PATCH request handling
    // Update a given message.

    // Get and sanitize the input data
    $postData = json_decode(file_get_contents("php://input"), true);
    $accepted = isset($postData['accepted']) ? filter_var($postData['accepted'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : null;
    $message_id = isset($postData['message_id']) ? filter_var($postData['message_id'], FILTER_VALIDATE_INT) : null;
    $user_id = $session->getId();

    if (!$user_id) {
      http_response_code(401); // Unauthorized
      echo json_encode(array("message" => "Not authenticated."));
      exit();
    }

    if ($session->getSessionToken() !== $postData['csrf']) {
      http_response_code(401); // Unauthorized
      echo json_encode(array("message" => "Unauthorized."));
      exit();
    }

    if ($message_id === null || $accepted === null) {
      http_response_code(400); // Bad Request
      echo json_encode(array("message" => "Invalid input."));
      exit();
    }

    try {
      $message = Message::getMessage($db, $message_id);

      if ($message->item_seller != $user_id) {
        http_response_code(401); // Unauthorized
        echo json_encode(array("message" => "Unauthorized."));
        exit();
      }

      $updatedMessage = Message::updateMessage($db, $message_id, $accepted);
      if ($updatedMessage) {
        http_response_code(200); // OK
        echo json_encode($updatedMessage);
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