<?php
declare(strict_types=1);

require_once (__DIR__ . '/../utils/session.php');
$session = new Session();
$id = $session->getId();

require_once (__DIR__ . '/../database/connection.db.php');
require_once (__DIR__ . '/../database/item.class.php');
require_once (__DIR__ . '/../database/user.class.php');

require_once (__DIR__ . '/../templates/header.tpl.php');
require_once (__DIR__ . '/../templates/footer.tpl.php');
require_once (__DIR__ . '/../templates/search-bar.tpl.php');
require_once (__DIR__ . '/../templates/breadcrumb-nav.tpl.php');
require_once (__DIR__ . '/../templates/item.tpl.php');

$db = getDatabaseConnection();

$item = Item::getItem($db, intval($_GET['id']));

drawHeader($session);
drawSearchBar();
// drawBreadcrumbNav();

if (!$item): ?>
  <h2>Ups! Página não encontrada. Por favor, verifique o URL e tente novamente.</h2>
<?php else:
  $item->increaseNumberOfClicks($db);
  $seller = User::getUser($db, $item->seller);
  $seller_reviews = User::getUserReviews($db, $item->seller);
  $is_item_in_wishlist = $id ? User::isItemInWishlist($db, $id, $item->id) : false;
  $is_item_in_cart = $id ? User::isItemInCart($db, $id, $item->id) : false;
  drawItem($item, $seller, $seller_reviews, $is_item_in_wishlist, $is_item_in_cart, $session);
endif;

drawFooter();
?>
<?php if ($item): ?>
  <script src="./../javascript/item.js"></script>
  <script>
    var sessionId = <?php echo json_encode($session->getId()); ?>;
    var itemId = <?php echo json_encode($item->id); ?>;
    initialize(sessionId, itemId);
  </script>
<?php endif; ?>