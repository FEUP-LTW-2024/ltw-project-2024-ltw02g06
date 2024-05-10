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
      $user = User::getUser($db, $id);
      $user->password = null;
      if ($user) {
        echo json_encode($user);
      } else {
        http_response_code(404); // Not Found
        echo json_encode(array("message" => "User not found."));
      }
    } else {
      $search = isset($_GET['search']) ? $_GET['search'] : [];

      try {
        $users = User::getAllUsers($db, $search);
        if ($users) {
          http_response_code(200); // OK
          echo json_encode($users);
        } else {
          http_response_code(404); // Not Found
          echo json_encode(array("message" => "No users found."));
        }
      } catch (PDOException $e) {
        http_response_code(500); // Internal Server Error
        echo json_encode(array("message" => $e->getMessage()));
      }
    }
    break;
  case 'POST':
    // POST request handling
    // TODO create code to create a new user
    break;
  case 'PATCH':
    // PATCH request handling
    // TODO create code to update a given user
    break;
  default:
    // Handle unsupported request methods
    http_response_code(405);
    exit("Unsupported request method");
}
?>