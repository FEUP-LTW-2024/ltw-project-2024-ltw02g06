<?php
declare(strict_types=1);

require_once (__DIR__ . '/../../utils/session.php');
$session = new Session();

require_once (__DIR__ . '/../../database/connection.db.php');
require_once (__DIR__ . '/../../database/message.class.php');

$db = getDatabaseConnection();

$requestMethod = $_SERVER['REQUEST_METHOD'];

switch ($requestMethod) {
    case 'GET':
        // GET request handling
        $userId = $session->getId();

        if ($userId === null) {
            http_response_code(401); // Unauthorized
            echo json_encode(array("message" => "Not authenticated."));
            exit();
        }

        if ($session->getSessionToken() !== $_GET['csrf']) {
            http_response_code(401); // Unauthorized
            echo json_encode(array("message" => "Unauthorized."));
            exit();
        }

        // Extract and sanitize the search parameter from the URL
        $search = isset($_GET['search']) ? filter_var($_GET['search'], FILTER_SANITIZE_STRING) : null;
        if ($search == "") {
            $search = null;
        }

        try {
            $inbox = Message::getInbox($db, $userId, $search);
            if ($inbox) {
                http_response_code(200); // OK
                echo json_encode($inbox);
            } else {
                http_response_code(404); // Not Found
                echo json_encode(array("message" => "No messages found."));
            }
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