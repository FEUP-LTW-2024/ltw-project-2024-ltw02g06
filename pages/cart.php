<?php
declare(strict_types=1);

require_once (__DIR__ . '/../utils/session.php');
$session = new Session();

$id = $session->getId();

if (!$id) {
  $redirectUrl = urlencode("/pages/cart.php");
  header("Location: login.php?redirect=$redirectUrl");
  exit();
}

require_once (__DIR__ . '/../database/connection.db.php');

require_once (__DIR__ . '/../templates/header.tpl.php');
require_once (__DIR__ . '/../templates/footer.tpl.php');
require_once (__DIR__ . '/../templates/search-bar.tpl.php');
require_once (__DIR__ . '/../templates/cart.tpl.php');

require_once (__DIR__ . '/../database/user.class.php');

$db = getDatabaseConnection();

$cart = User::getCart($db, $id);
$user = User::getUser($db, $id);
drawHeader($session);
drawCart($session, $cart);
drawFooter();
?>
<script>
  const userId = <?php echo json_encode($id); ?>;
  const cart = <?php echo json_encode($cart); ?>;
</script>
<!-- <script src="./../javascript/utils.js"></script> -->
<script src="./../javascript/cart.js"></script>