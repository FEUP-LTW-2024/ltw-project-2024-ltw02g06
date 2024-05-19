<?php
declare(strict_types=1);

require_once (__DIR__ . '/../../utils/session.php');
$session = new Session();

require_once (__DIR__ . '/../../database/connection.db.php');
require_once (__DIR__ . '/../../database/user.class.php');

$db = getDatabaseConnection();

$request_method = $_SERVER['REQUEST_METHOD'];

switch ($request_method) {
  case 'POST':
    // POST request handling for login
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $user = User::getUserWithPassword($db, $email, $password);

    if ($user) {
      $session->setId($user->id);
      $session->setName($user->name());
      $session->generateSessionToken();

      http_response_code(200); // OK
      echo json_encode(array("message" => "Authenticated successfuly!"));
    } else {
      http_response_code(401); // Unauthorized
      echo json_encode(array("message" => "Wrong credentials!"));
    }
    break;
  case 'PATCH':
    // PATCH request handling for password change
    $requestData = json_decode(file_get_contents('php://input'), true);

    $oldPassword = filter_var($requestData['password'], FILTER_SANITIZE_STRING);
    $newPassword = filter_var($requestData['newPassword'], FILTER_SANITIZE_STRING);
    $confirmNewPassword = filter_var($requestData['confirmNewPassword'], FILTER_SANITIZE_STRING);

    $id = $session->getId();

    if (!$id) {
      http_response_code(401); // Unauthorized
      echo json_encode(array("message" => "Not authenticated."));
      exit();
    }

    if (empty($oldPassword) || empty($newPassword) || empty($confirmNewPassword)) {
      http_response_code(400); // Bad Request
      echo json_encode(array("message" => "Invalid input."));
      exit();
    }

    if ($newPassword !== $confirmNewPassword) {
      http_response_code(400); // Bad Request
      echo json_encode(array("message" => "Passwords do not match."));
      exit();
    }

    $email = User::getUser($db, $id)->email;
    $user = User::getUserWithPassword($db, $email, $oldPassword);

    if ($user) {
      User::changeUserPassword($db, $id, $newPassword);
      http_response_code(200); // OK
      echo json_encode(array("message" => "Password changed successfully."));
    } else {
      http_response_code(401); // Unauthorized
      echo json_encode(array("message" => "Wrong credentials."));
    }
    break;
  default:
    // Handle unsupported request methods
    http_response_code(405);
    echo json_encode(['message' => 'Unsupported request method']);
    break;
}
?>