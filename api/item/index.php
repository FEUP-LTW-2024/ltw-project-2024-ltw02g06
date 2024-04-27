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
    $user_id = $session->getId();
    $getTotal = isset($_GET['total']) ? boolval($_GET['total']) : 0;

    if ($id !== null) {
      $item = Item::getItem($db, $id);
      if ($item) {
        echo json_encode($item);
      } else {
        http_response_code(404); // Not Found
        echo json_encode(array("message" => "Item not found."));
      }
    } else if ($getTotal) {
      $search = isset($_GET['search']) ? $_GET['search'] : [];

      try {
        $total = Item::getItemsTotal($db, $search);
        if ($total != null) {
          http_response_code(200); // OK
          echo json_encode($total);
        } else {
          http_response_code(404); // Not Found
          echo json_encode(array("message" => "No items found."));
        }
      } catch (PDOException $e) {
        http_response_code(500); // Internal Server Error
        echo json_encode(array("message" => $e->getMessage()));
      }
    } else {
      // Extract parameters from the URL
      $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
      $items_per_page = isset($_GET['itemsPerPage']) ? intval($_GET['itemsPerPage']) : 10;
      $search = isset($_GET['search']) ? $_GET['search'] : [];

      try {
        $items = Item::getAllItems($db, $user_id, $page, $items_per_page, $search);
        if ($items) {
          http_response_code(200); // OK
          echo json_encode($items);
        } else {
          http_response_code(404); // Not Found
          echo json_encode(array("message" => "No items found."));
        }
      } catch (PDOException $e) {
        http_response_code(500); // Internal Server Error
        echo json_encode(array("message" => $e->getMessage()));
      }
    }
    break;
  case 'POST':
    // POST request handling
    // Create a item
    $postData = json_decode(file_get_contents('php://input'), true);
    $user_id = $session->getId();

    if (!$user_id) {
      http_response_code(401); // Unauthorized
      echo json_encode(array("message" => "Not authenticated."));
      exit();
    }

    try {
      $item = Item::createItem($db, $postData);
      if ($item) {
        http_response_code(201); // Created
        echo json_encode($item);
      } else {
        http_response_code(400); // Bad Request
        echo json_encode(array("message" => "Unable to create item."));
      }
    } catch (PDOException $e) {
      http_response_code(500); // Internal Server Error
      echo json_encode(array("message" => $e->getMessage()));
    }
    break;
  case 'PATCH':
    // PATCH request handling
    // Update a given item.
    $postData = json_decode(file_get_contents('php://input'), true);
    $user_id = $session->getId();

    if (!$user_id || $user_id != $item->seller) {
      http_response_code(401); // Unauthorized
      echo json_encode(array("message" => "Not authenticated."));
      exit();
    }

    try {
      $item = Item::updateItem($db, $postData);
      if ($item) {
        http_response_code(200); // OK
        echo json_encode($item);
      } else {
        http_response_code(400); // Bad Request
        echo json_encode(array("message" => "Unable to update item."));
      }
    } catch (PDOException $e) {
      http_response_code(500); // Internal Server Error
      echo json_encode(array("message" => $e->getMessage()));
    }
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