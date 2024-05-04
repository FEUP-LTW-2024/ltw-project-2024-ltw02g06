<?php
declare(strict_types=1);

require_once (__DIR__ . '/../utils/session.php');

$session = new Session();

$id = $session->getId();

if (!$id) {
  $redirectUrl = urlencode("/pages/profile.php");
  header("Location: login.php?redirect=$redirectUrl");
  exit();
}

require_once (__DIR__ . '/../database/connection.db.php');

require_once (__DIR__ . '/../templates/header.tpl.php');
require_once (__DIR__ . '/../templates/footer.tpl.php');
require_once (__DIR__ . '/../templates/profile.tpl.php');
require_once (__DIR__ . '/../database/user.class.php');

$db = getDatabaseConnection();

$user = User::getUser($db, $id);

drawHeader($session);
drawEditProfile($session, $user);
drawFooter();
?>
<script src="./../javascript/editProfile.js"></script>