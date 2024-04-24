<?php
declare(strict_types=1);

require_once (__DIR__ . '/../../utils/session.php');
$session = new Session();

require_once (__DIR__ . '/../../database/connection.db.php');
require_once (__DIR__ . '/../../database/item.class.php');

$db = getDatabaseConnection();

$request_method = $_SERVER['REQUEST_METHOD'];

switch ($request_method) {
  case 'GET':
    // GET request handling
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;
    if ($id !== null) {
      $item = Item::getItem($db, $id);
      if ($item) {
        echo json_encode($item);
      } else {
        http_response_code(404); // Not Found
        echo json_encode(array("message" => "Item not found."));
      }
    } else {
      http_response_code(400); // Bad Request
      echo json_encode(array("message" => "Item ID is required for GET request."));
    }
    break;
  case 'POST':
    // POST request handling
    $postData = json_decode(file_get_contents('php://input'), true);
    $item = Item::createItem($db, $postData);
    if ($item) {
      http_response_code(201); // Created
      echo json_encode($item);
    } else {
      http_response_code(400); // Bad Request
      echo json_encode(array("message" => "Unable to create item."));
    }
    break;
  case 'PATCH':
    // PATCH request handling
    // TODO create code to update a given item
    break;
  case 'DELETE':
    // DELETE request handling
    // Delete an item.
    $user_id = $session->getId();
    $id = (int) $_GET['id'];

    if (!$user_id) {
      http_response_code(401); // Unauthorized
      echo json_encode(array("message" => "Not authenticated."));
      exit();
    }

    if (!isset($id)) {
      http_response_code(400); // Bad Request
      echo json_encode(array("message" => "Item ID is required."));
      exit();
    }

    $item = Item::getItem($db, $id);

    if ($user_id != $item->seller) {
      http_response_code(401); // Unauthorized
      echo json_encode(array("message" => "Not authenticated."));
      exit();
    }

    try {
      Item::deleteItem($db, $id);
      http_response_code(200); // OK
      echo json_encode(array("message" => "Item deleted."));
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