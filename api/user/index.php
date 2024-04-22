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
    // TODO create code to get a user
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