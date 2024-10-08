<?php
declare(strict_types=1);

require_once (__DIR__ . '/../utils/session.php');
$session = new Session();
$id = $session->getId();

require_once (__DIR__ . '/../database/connection.db.php');
require_once (__DIR__ . '/../database/item.class.php');
require_once (__DIR__ . '/../database/user.class.php');
require_once (__DIR__ . '/../database/category.class.php');

require_once (__DIR__ . '/../templates/header.tpl.php');
require_once (__DIR__ . '/../templates/footer.tpl.php');
require_once (__DIR__ . '/../templates/item.tpl.php');

$db = getDatabaseConnection();

$item = Item::getItem($db, intval($_GET['id']));

drawHeader($session);

if (!$item || $item->seller != $id || $item->status != 'active'): ?>
  <h2>Ups! Página não encontrada. Por favor, verifique o URL e tente novamente.</h2>
<?php else:
  $user = User::getUser($db, $id);
  $userReviews = User::getUserReviews($db, $id);
  $categories = Category::getAllCategories($db);
  drawEditItem($item, $user, $userReviews, $categories, $session);
endif;

drawFooter();
?>
<?php if ($item && $item->seller == $id): ?>
  <script>
    const csrf = <?php echo json_encode($session->getSessionToken()) ?>;
    const item = JSON.parse(JSON.stringify(<?php echo json_encode($item); ?>));
    const categories = JSON.parse(JSON.stringify(<?php echo json_encode($categories); ?>));
  </script>
  <script src="./../javascript/utils.js"></script>
  <script src="./../javascript/editItem.js"></script>

<?php endif; ?>