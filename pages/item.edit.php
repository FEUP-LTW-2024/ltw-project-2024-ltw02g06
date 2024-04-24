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

if (!$item || $item->seller != $id): ?>
  <h2>Ups! Página não encontrada. Por favor, verifique o URL e tente novamente.</h2>
<?php else:
  $user = User::getUser($db, $id);
  $user_reviews = User::getUserReviews($db, $id);
  $categories = Category::getAllCategories($db);
  drawEditItem($item, $user, $user_reviews, $categories, $session);
endif;

drawFooter();
?>
<?php if ($item && $item->seller == $id): ?>
  <script src="./../javascript/editItem.js"></script>
  <script>
    var item = JSON.parse(JSON.stringify(<?php echo json_encode($item); ?>));
    var categories = JSON.parse(JSON.stringify(<?php echo json_encode($categories); ?>));
    initialize(item, categories);
  </script>
<?php endif; ?>