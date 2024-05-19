<?php
declare(strict_types=1);

require_once (__DIR__ . '/../../utils/session.php');
$session = new Session();

require_once (__DIR__ . '/../../database/connection.db.php');
require_once (__DIR__ . '/../../database/item.class.php');
require_once (__DIR__ . '/../../database/user.class.php');

$db = getDatabaseConnection();

$requestMethod = $_SERVER['REQUEST_METHOD'];

switch ($requestMethod) {
    case 'GET':
        // GET request handling
        $id = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : null;
        $userId = $session->getId();
        $getTotal = isset($_GET['total']) ? filter_var($_GET['total'], FILTER_VALIDATE_BOOLEAN) : false;

        if ($id !== null) {
            $item = Item::getItem($db, $id);
            if ($item) {
                echo json_encode($item);
            } else {
                http_response_code(404); // Not Found
                echo json_encode(array("message" => "Item not found."));
            }
        } else if ($getTotal) {
            $search = isset($_GET['search']) ? filter_var_array($_GET['search'], FILTER_SANITIZE_STRING) : [];
            $sellerId = isset($_GET['user']) ? filter_var($_GET['user'], FILTER_VALIDATE_INT) : null;
            $active = isset($_GET['status']) ? ($_GET['status'] == "active") : true;

            try {
                $total = Item::getItemsTotal($db, $sellerId, $search, $active);
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
            $page = isset($_GET['page']) ? filter_var($_GET['page'], FILTER_VALIDATE_INT) : 1;
            $itemsPerPage = isset($_GET['itemsPerPage']) ? filter_var($_GET['itemsPerPage'], FILTER_VALIDATE_INT) : 10;
            $search = isset($_GET['search']) ? filter_var_array($_GET['search'], FILTER_SANITIZE_STRING) : [];
            $sellerId = isset($_GET['user']) ? filter_var($_GET['user'], FILTER_VALIDATE_INT) : null;
            $active = isset($_GET['status']) ? ($_GET['status'] == "active") : true;

            try {
                $items = Item::getAllItems($db, $userId, $sellerId, $page, $itemsPerPage, $search, $active);
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
        // Create an item
        $postData = json_decode(file_get_contents('php://input'), true);
        $userId = $session->getId();

        if (!$userId) {
            http_response_code(401); // Unauthorized
            echo json_encode(array("message" => "Not authenticated."));
            exit();
        }

        if ($session->getSessionToken() !== $postData['csrf']) {
            http_response_code(401); // Unauthorized
            echo json_encode(array("message" => "Unauthorized."));
            exit();
        }

        try {
            // Add necessary sanitization for $postData fields
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
        $userId = $session->getId();

        if (!$userId || !isset($postData['id']) || !filter_var($postData['id'], FILTER_VALIDATE_INT)) {
            http_response_code(401); // Unauthorized
            echo json_encode(array("message" => "Not authenticated or invalid item ID."));
            exit();
        }

        try {
            $item = Item::getItem($db, $postData['id']);
            if ($item->seller != $userId || $session->getSessionToken() !== $postData['csrf']) {
                http_response_code(401); // Unauthorized
                echo json_encode(array("message" => "Unauthorized."));
                exit();
            }

            // Add necessary sanitization for $postData fields
            $updatedItem = Item::updateItem($db, $postData);
            if ($updatedItem) {
                http_response_code(200); // OK
                echo json_encode($updatedItem);
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
        $userId = $session->getId();
        $postData = json_decode(file_get_contents('php://input'), true);
        $id = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : null;

        if (!$userId) {
            http_response_code(401); // Unauthorized
            echo json_encode(array("message" => "Not authenticated."));
            exit();
        }

        if ($id === null) {
            http_response_code(400); // Bad Request
            echo json_encode(array("message" => "Item ID is required."));
            exit();
        }

        $item = Item::getItem($db, $id);
        $user = User::getUser($db, $userId);

        if (
            ($userId != $item->seller && !$user->admin) ||
            $session->getSessionToken() !== $postData['csrf']
        ) {
            http_response_code(401); // Unauthorized
            echo json_encode(array("message" => "Unauthorized."));
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