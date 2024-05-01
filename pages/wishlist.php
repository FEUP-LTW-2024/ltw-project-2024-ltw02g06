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

$wishlist = User::getWishlist($db, $id);

drawHeader($session);
drawWishlistItems($session, $wishlist);
drawFooter();
?>
<script>
  const userId = <?php echo json_encode($id); ?>;
  const wishlist = <?php echo json_encode($wishlist); ?>;
</script>
<!-- <script src="./../javascript/utils.js"></script> -->
<script src="./../javascript/wishlist.js"></script>