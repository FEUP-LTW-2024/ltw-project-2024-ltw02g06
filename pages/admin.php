<?php
declare(strict_types=1);

require_once (__DIR__ . '/../utils/session.php');
$session = new Session();
$id = $session->getId();

if (!$id) {
  $redirectUrl = urlencode("/pages/admin.php");
  header("Location: ../pages/login.php?redirect=$redirectUrl");
  exit();
}

require_once (__DIR__ . '/../database/connection.db.php');
require_once (__DIR__ . '/../database/user.class.php');

require_once (__DIR__ . '/../templates/header.tpl.php');
require_once (__DIR__ . '/../templates/footer.tpl.php');
require_once (__DIR__ . '/../templates/admin.tpl.php');

$db = getDatabaseConnection();

$user = User::getUser($db, $id);

drawHeader($session);

if (!$user->admin) { ?>
  <h2>Ups! Página não encontrada. Por favor, verifique o URL e tente novamente.</h2>
  <?php
  drawFooter();
  exit();
}

drawAdminCategoriesSection();
drawAdminUsersSection();
drawFooter();
?>
<script>
  const csrf = <?php echo json_encode($session->getSessionToken()) ?>;
  const sessionId = <?php echo json_encode($id); ?>;
</script>
<script src="./../javascript/utils.js"></script>
<script src="./../javascript/admin.js"></script>