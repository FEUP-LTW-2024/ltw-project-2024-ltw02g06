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
    // Extract parameters from the URL
    $search = isset($_GET['search']) ? $_GET['search'] : null;
    if ($search == "")
      $search = null;

    try {
      $inbox = Message::getInbox($db, $user_id, $search);
      if ($inbox) {
        http_response_code(200); // OK
        echo json_encode($inbox);
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