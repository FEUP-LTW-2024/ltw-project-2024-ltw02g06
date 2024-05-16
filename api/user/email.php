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
    // Check if email is already registered
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['email'])) {
      $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);

      if ($email === false) {
        // Invalid email format
        http_response_code(400);
        echo json_encode(array("message" => "Invalid email format"));
      } else {
        // Email format is valid, check if it's registered
        $is_registered = User::isEmailRegistered($db, $email);

        if ($is_registered) {
          // Email is already registered
          http_response_code(200);
          echo json_encode(array("message" => "Email is already registered"));
        } else {
          // Email is not registered
          http_response_code(404);
          echo json_encode(array("message" => "Email is not registered"));
        }
      }
    } else {
      // Email parameter is missing in the request
      http_response_code(400);
      echo json_encode(array("message" => "Email parameter is missing"));
    }
    break;
  default:
    // Handle unsupported request methods
    http_response_code(405);
    exit("Unsupported request method");
}
?>