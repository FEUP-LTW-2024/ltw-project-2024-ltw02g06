<?php
declare(strict_types=1);

require_once (__DIR__ . '/../utils/session.php');

$session = new Session();

$id = $session->getId();

if (!$id && !isset($_GET['user'])) {
  $redirectUrl = urlencode("/pages/profile.php");
  header("Location: login.php?redirect=$redirectUrl");
  exit();
}

if (!isset($_GET['user'])) {
  header("Location: /pages/profile.php?user=$id");
}

require_once (__DIR__ . '/../database/connection.db.php');

require_once (__DIR__ . '/../templates/header.tpl.php');
require_once (__DIR__ . '/../templates/footer.tpl.php');
require_once (__DIR__ . '/../templates/profile.tpl.php');
require_once (__DIR__ . '/../templates/item.tpl.php');
require_once (__DIR__ . '/../database/user.class.php');

$db = getDatabaseConnection();

drawHeader($session);

$user = User::getUser($db, intval($_GET['user']));

if (!$user): ?>
  <h2>Ups! Página não encontrada. Por favor, verifique o URL e tente novamente.</h2>
  <?php
  exit();
endif;

$search = isset($_GET['search']) ? $_GET['search'] : null;
$order = isset($search['order']) ? $search['order'] : 'relevance:desc';

drawProfile($session, $user);
drawSmallSearchBar();
drawProfileItems($session, $order);
drawFooter();
?>
<script src="./../javascript/utils.js"></script>
<script src="./../javascript/profile.js"></script>