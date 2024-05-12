<?php
declare(strict_types=1);

require_once (__DIR__ . '/../../utils/session.php');
$session = new Session();

require_once (__DIR__ . '/../../database/connection.db.php');
require_once (__DIR__ . '/../../database/category.class.php');
require_once (__DIR__ . '/../../database/user.class.php');

$db = getDatabaseConnection();

$request_method = $_SERVER['REQUEST_METHOD'];

switch ($request_method) {
  case 'GET':
    // GET request handling
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;
    if ($id !== null) {
      $category = Category::getCategory($db, $id);
      if ($category) {
        http_response_code(200); // OK
        echo json_encode($category);
      } else {
        http_response_code(404); // Not Found
        echo json_encode(array("message" => "Category not found."));
      }
    } else {
      $categories = Category::getAllCategories($db);
      if ($categories) {
        http_response_code(200); // OK
        echo json_encode($categories);
      } else {
        http_response_code(404); // Not Found
        echo json_encode(array("message" => "No categories found."));
      }
    }
    break;
  case 'POST':
    // POST request handling

    $postData = json_decode(file_get_contents('php://input'), true);
    $user_id = $session->getId();
    $user = User::getUser($db, $user_id);

    if (!$user_id) {
      http_response_code(401); // Unauthorized
      echo json_encode(array("message" => "Not authenticated."));
      exit();
    }

    if (!$user->admin) {
      http_response_code(401); // Unauthorized
      echo json_encode(array("message" => "Unauthorized."));
      exit();
    }

    try {
      $category = Category::createCategory($db, $postData);
      if ($category) {
        http_response_code(201); // Created
        echo json_encode($category);
      } else {
        http_response_code(400); // Bad Request
        echo json_encode(array("message" => "Unable to create category."));
      }
    } catch (PDOException $e) {
      http_response_code(500); // Internal Server Error
      echo json_encode(array("message" => $e->getMessage()));
    }
    break;
  case 'PATCH':
    // PATCH request handling
    // TODO create code to update a given category
    break;
  case 'DELETE':
    // DELETE request handling
    // Delete a given category
    $categoryId = (int) $_GET['id'];
    $user_id = $session->getId();
    $user = User::getUser($db, $user_id);

    if (!$user_id) {
      http_response_code(401); // Unauthorized
      echo json_encode(array("message" => "Not authenticated."));
      exit();
    }

    if (!$user->admin) {
      http_response_code(401); // Unauthorized
      echo json_encode(array("message" => "Unauthorized."));
      exit();
    }

    try {
      Category::deleteCategory($db, $categoryId);
      http_response_code(200); // OK
      echo json_encode(array("message" => "Success!"));
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