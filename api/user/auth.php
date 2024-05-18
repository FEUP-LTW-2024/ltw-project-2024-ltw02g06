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

      $response = [
        'status' => 'success',
        'redirect' => isset($_GET['redirect']) ? htmlspecialchars($_GET['redirect'], ENT_QUOTES, 'UTF-8') : $_SERVER['HTTP_REFERER']
      ];
      http_response_code(200); // OK
    } else {
      $response = ['status' => 'error', 'message' => 'Wrong credentials!'];
      http_response_code(401); // Unauthorized
    }

    echo json_encode($response);
    break;

  default:
    // Handle unsupported request methods
    http_response_code(405);
    echo json_encode(['message' => 'Unsupported request method']);
    break;
}
?>