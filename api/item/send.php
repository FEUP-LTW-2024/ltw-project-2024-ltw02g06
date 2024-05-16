<?php
declare(strict_types=1);

require_once (__DIR__ . '/../../utils/session.php');
$session = new Session();

require_once (__DIR__ . '/../../database/connection.db.php');
require_once (__DIR__ . '/../../database/item.class.php');

$db = getDatabaseConnection();

$request_method = $_SERVER['REQUEST_METHOD'];

switch ($request_method) {
  case 'POST':
    // POST request handling
    // Send item

    $user_id = $session->getId();

    if (!$user_id) {
      http_response_code(401); // Unauthorized
      echo json_encode(array("message" => "Not authenticated."));
      exit();
    }

    $id = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : null;

    if (!$id) {
      http_response_code(400); // Bad Request
      echo json_encode(array("message" => "Item ID is required."));
      exit();
    }

    try {
      $item = Item::getItem($db, $id);

      if (!$item || $item->seller != $user_id) {
        http_response_code(401); // Unauthorized
        echo json_encode(array("message" => "Unauthorized."));
        exit();
      }

      $item = Item::sendItem($db, $id);
      if ($item) {
        http_response_code(200); // OK
        echo json_encode($item);
      } else {
        http_response_code(400); // Bad Request
        echo json_encode(array("message" => "Unable to send item."));
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