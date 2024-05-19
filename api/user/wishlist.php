<?php
declare(strict_types=1);

require_once (__DIR__ . '/../../utils/session.php');
$session = new Session();

require_once (__DIR__ . '/../../database/connection.db.php');
require_once (__DIR__ . '/../../database/user.class.php');
require_once (__DIR__ . '/../../database/item.class.php');

$db = getDatabaseConnection();

$request_method = $_SERVER['REQUEST_METHOD'];

switch ($request_method) {
  case 'GET':
    // GET request handling
    // Retrieve the wishlist of a given user
    $user_id = $session->getId();

    if (!isset($user_id)) {
      http_response_code(401); // Unauthorized
      echo json_encode(array("message" => "Not authenticated."));
      exit();
    }

    try {
      $wishlist = User::getWishlist($db, $user_id);
      http_response_code(200); // OK
      echo json_encode(array("wishlist" => $wishlist));
    } catch (PDOException $e) {
      http_response_code(500); // Internal Server Error
      echo json_encode(array("message" => $e->getMessage()));
    }
    break;
  case 'POST':
    // POST request handling
    // Add a new item to the wishlist of a given user
    $user_id = $session->getId();
    $postData = filter_var_array(json_decode(file_get_contents("php://input"), true), FILTER_SANITIZE_STRING);

    if (!isset($user_id)) {
      http_response_code(401); // Unauthorized
      echo json_encode(array("message" => "Not authenticated."));
      exit();
    }

    if ($session->getSessionToken() !== $postData['csrf']) {
      http_response_code(401); // Unauthorized
      echo json_encode(array("message" => "Unauthorized."));
      exit();
    }

    // Validate and sanitize item_id
    $item_id = isset($postData['item_id']) ? filter_var($postData['item_id'], FILTER_VALIDATE_INT) : null;

    if (!isset($item_id)) {
      http_response_code(400); // Bad Request
      echo json_encode(array("message" => "Item ID is required."));
      exit();
    }

    try {
      $item = Item::getItem($db, $item_id);
      if (!$item || $item->status != 'active') {
        http_response_code(404); // Not Found
        echo json_encode(array("message" => "Item not found or already sold."));
        exit();
      }
      if ($item->seller === $user_id) {
        http_response_code(403); // Forbidden
        echo json_encode(array("message" => "You can't add your own items to your wishlist."));
        exit();
      }

      User::addItemToWishlist($db, $user_id, $item_id);
      http_response_code(201); // Created
      echo json_encode(array("message" => "Item added to wishlist."));
    } catch (PDOException $e) {
      http_response_code(500); // Internal Server Error
      echo json_encode(array("message" => $e->getMessage()));
    }
    break;
  case 'DELETE':
    // DELETE request handling
    // Remove an item from the wishlist of a given user
    $user_id = $session->getId();
    $item_id = isset($_GET['item_id']) ? filter_var($_GET['item_id'], FILTER_VALIDATE_INT) : null;

    if (!isset($user_id)) {
      http_response_code(401); // Unauthorized
      echo json_encode(array("message" => "Not authenticated."));
      exit();
    }

    if (!isset($item_id)) {
      http_response_code(400); // Bad Request
      echo json_encode(array("message" => "Item ID is required."));
      exit();
    }

    try {
      User::removeItemFromWishlist($db, $user_id, $item_id);
      http_response_code(200); // OK
      echo json_encode(array("message" => "Item removed from wishlist."));
    } catch (PDOException $e) {
      http_response_code(500); // Internal Server Error
      echo json_encode(array("message" => $e->getMessage()));
    }
    break;
  default:
    // Handle unsupported request methods
    http_response_code(405);
    echo json_encode(array("message" => "Unsupported request method"));
}
?>