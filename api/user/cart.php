<?php
declare(strict_types=1);

require_once (__DIR__ . '/../../utils/session.php');
$session = new Session();

require_once (__DIR__ . '/../../database/connection.db.php');
require_once (__DIR__ . '/../../database/user.class.php');
require_once (__DIR__ . '/../../database/item.class.php');
require_once (__DIR__ . '/../../database/message.class.php');

$db = getDatabaseConnection();

$request_method = $_SERVER['REQUEST_METHOD'];

switch ($request_method) {
  case 'GET':
    // GET request handling
    // TODO create code to get the cart of a given user
    break;
  case 'POST':
    // POST request handling
    // Add a new item to the cart of a given user
    $user_id = $session->getId();
    $postData = json_decode(file_get_contents("php://input"), true);
    $checkout = isset($postData['checkout']) ? filter_var($postData['checkout'], FILTER_VALIDATE_BOOLEAN) : false;
    $item_id = isset($postData['item_id']) ? filter_var($postData['item_id'], FILTER_VALIDATE_INT) : null;
    $message_id = isset($postData['message_id']) ? filter_var($postData['message_id'], FILTER_VALIDATE_INT) : null;

    if (!isset($user_id)) {
      http_response_code(401); // Unauthorized
      echo json_encode(array("message" => "Not authenticated."));
      exit();
    }

    if ($checkout) {
      try {
        User::purchaseCart($db, $user_id);
        http_response_code(200); // OK
        echo json_encode(array("message" => "All items in the cart were purchased."));
      } catch (PDOException $e) {
        http_response_code(500); // Internal Server Error
        echo json_encode(array("message" => $e->getMessage()));
      }
    } else {
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
          echo json_encode(array("message" => "You can't add your own items to your cart."));
          exit();
        }
        $message = $message_id ? Message::getMessage($db, $message_id) : null;
        $price = $item->price;
        if ($message && $message->type == 'negotiation' && $message->item_id == $item_id && $message->accepted) {
          $price = $message->value;
        }
        $cartItem = User::addItemToCart($db, $user_id, $item_id, $price);
        http_response_code(201); // Created
        echo json_encode(array("message" => "Item added to cart.", "cartItem" => $cartItem));
      } catch (PDOException $e) {
        http_response_code(500); // Internal Server Error
        echo json_encode(array("message" => $e->getMessage()));
      }
    }
    break;
  case 'DELETE':
    // DELETE request handling
    // Remove an item from the cart of a given user
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
      User::removeItemFromCart($db, $user_id, $item_id);
      http_response_code(200); // OK
      echo json_encode(array("message" => "Item removed from cart."));
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