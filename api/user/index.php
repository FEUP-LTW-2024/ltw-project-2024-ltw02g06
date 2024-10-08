<?php
declare(strict_types=1);

require_once (__DIR__ . '/../../utils/session.php');
$session = new Session();

require_once (__DIR__ . '/../../database/connection.db.php');
require_once (__DIR__ . '/../../database/item.class.php');

$db = getDatabaseConnection();

$requestMethod = $_SERVER['REQUEST_METHOD'];

switch ($requestMethod) {
  case 'GET':
    // GET request handling
    $id = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : null;

    if ($id !== null) {
      // No further sanitization needed for $id
      $user = User::getUser($db, $id);
      $user->password = null;
      if ($user) {
        echo json_encode($user);
      } else {
        http_response_code(404); // Not Found
        echo json_encode(array("message" => "User not found."));
      }
    } else {
      $search = isset($_GET['search']) ? filter_var_array($_GET['search'], FILTER_SANITIZE_STRING) : [];

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
    $userData = filter_var_array(json_decode(file_get_contents('php://input'), true), [
      'email' => FILTER_VALIDATE_EMAIL,
      'password' => FILTER_SANITIZE_STRING,
      'confirmPassword' => FILTER_SANITIZE_STRING,
      'firstName' => FILTER_SANITIZE_STRING,
      'lastName' => FILTER_SANITIZE_STRING,
      'address' => FILTER_SANITIZE_STRING,
      'zipcode' => FILTER_SANITIZE_STRING,
      'city' => FILTER_SANITIZE_STRING,
      'state' => FILTER_SANITIZE_STRING,
      'country' => FILTER_SANITIZE_STRING,
    ]);

    foreach ($userData as $key => $value) {
      $userData[$key] = trim($value);
    }

    if (
      empty($userData['email']) ||
      empty($userData['password']) ||
      empty($userData['confirmPassword']) ||
      empty($userData['firstName']) ||
      empty($userData['lastName']) ||
      empty($userData['address']) ||
      empty($userData['zipcode']) ||
      empty($userData['city']) ||
      empty($userData['state']) ||
      empty($userData['country'])
    ) {
      http_response_code(400); // Bad Request
      echo json_encode(array("message" => "Invalid input."));
      exit();
    }

    if ($userData['password'] !== $userData['confirmPassword']) {
      http_response_code(400); // Bad Request
      echo json_encode(array("message" => "Passwords do not match."));
      exit();
    }

    try {
      $user = User::createUser($db, $userData['email'], $userData['password'], $userData['firstName'], $userData['lastName'], $userData['address'], $userData['zipcode'], $userData['city'], $userData['state'], $userData['country']);
      if ($user) {
        $session->setId($user->id);
        $session->setName($user->name());
        $session->generateSessionToken();

        $response = [
          'status' => 'success',
          'redirect' => isset($_GET['redirect']) ? htmlspecialchars($_GET['redirect'], ENT_QUOTES, 'UTF-8') : $_SERVER['HTTP_REFERER']
        ];
        http_response_code(201); // Created
        echo json_encode(array("message" => "User created successfully."));
      } else {
        throw new PDOException;
      }
    } catch (PDOException $e) {
      http_response_code(500); // Internal Server Error
      echo json_encode(array("message" => $e->getMessage()));
    }
    break;
  case 'PATCH':
    // PATCH request handling
    // Update a given user
    $userData = filter_var_array(json_decode(file_get_contents('php://input'), true), FILTER_SANITIZE_STRING);
    $userId = $session->getId();
    $user = User::getUser($db, $userId);

    if (!$userId) {
      http_response_code(401); // Unauthorized
      echo json_encode(array("message" => "Not authenticated."));
      exit();
    }

    if (
      ($userId != filter_var($userData['id'], FILTER_VALIDATE_INT) && !$user->admin) ||
      $session->getSessionToken() !== filter_var($userData['csrf'], FILTER_SANITIZE_STRING)
    ) {
      http_response_code(401); // Unauthorized
      echo json_encode(array("message" => "Unauthorized."));
      exit();
    }

    $updateAdminStatus = isset($_GET['admin']) ? filter_var($_GET['admin'], FILTER_VALIDATE_BOOLEAN) : false;

    if ($updateAdminStatus && $user->admin) {
      try {
        User::updateAdminStatus($db, $userData);
        $user = User::getUser($db, filter_var($userData['id'], FILTER_VALIDATE_INT));
        if ($user) {
          $user->password = null;
          http_response_code(200); // OK
          echo json_encode($user);
        } else {
          http_response_code(400); // Bad Request
          echo json_encode(array("message" => "Unable to update user."));
        }
      } catch (PDOException $e) {
        http_response_code(500); // Internal Server Error
        echo json_encode(array("message" => $e->getMessage()));
      }
      break;
    }

    try {
      User::updateUser($db, $userData);
      $user = User::getUser($db, filter_var($userData['id'], FILTER_VALIDATE_INT));
      if ($user) {
        $user->password = null;
        http_response_code(200); // OK
        echo json_encode($user);
      } else {
        http_response_code(400); // Bad Request
        echo json_encode(array("message" => "Unable to update user."));
      }
    } catch (PDOException $e) {
      http_response_code(500); // Internal Server Error
      echo json_encode(array("message" => $e->getMessage()));
    }
    break;
  case 'DELETE':
    // DELETE request handling
    // Delete a user.
    $toDeleteUserId = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : null;
    $userId = $session->getId();
    $user = User::getUser($db, $userId);

    if (!$userId) {
      http_response_code(401); // Unauthorized
      echo json_encode(array("message" => "Not authenticated."));
      exit();
    }

    if ($toDeleteUserId === null) {
      http_response_code(400); // Bad Request
      echo json_encode(array("message" => "User ID is required."));
      exit();
    }

    if (
      ($userId != $toDeleteUserId && !$user->admin) ||
      $session->getSessionToken() !== filter_var($_POST['csrf'], FILTER_SANITIZE_STRING)
    ) {
      http_response_code(401); // Unauthorized
      echo json_encode(array("message" => "Unauthorized."));
      exit();
    }

    try {
      User::deleteUser($db, $toDeleteUserId);
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