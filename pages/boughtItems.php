<?php
declare(strict_types=1);

require_once (__DIR__ . '/../utils/session.php');
$session = new Session();

$id = $session->getId();

if (!$id) {
  $redirectUrl = urlencode("/pages/wishlist.php");
  header("Location: login.php?redirect=$redirectUrl");
  exit();
}

require_once (__DIR__ . '/../database/connection.db.php');

require_once (__DIR__ . '/../templates/header.tpl.php');
require_once (__DIR__ . '/../templates/footer.tpl.php');
require_once (__DIR__ . '/../templates/search-bar.tpl.php');
require_once (__DIR__ . '/../templates/item.tpl.php');

require_once (__DIR__ . '/../database/user.class.php');

$db = getDatabaseConnection();

$boughtItems = User::getBoughtItems($db, $id);

drawHeader($session);
drawBoughtItems($session, $boughtItems);
drawFooter();
?>
<script src="./../javascript/boughtItems.js"></script>