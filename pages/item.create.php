<?php
declare(strict_types=1);

require_once (__DIR__ . '/../utils/session.php');
$session = new Session();
$id = $session->getId();

if (!$id) {
  $redirectUrl = urlencode("/pages/item.create.php");
  header("Location: login.php?redirect=$redirectUrl");
  exit();
}

require_once (__DIR__ . '/../database/connection.db.php');
require_once (__DIR__ . '/../database/item.class.php');
require_once (__DIR__ . '/../database/user.class.php');
require_once (__DIR__ . '/../database/category.class.php');

require_once (__DIR__ . '/../templates/header.tpl.php');
require_once (__DIR__ . '/../templates/footer.tpl.php');
require_once (__DIR__ . '/../templates/item.tpl.php');

$db = getDatabaseConnection();

drawHeader($session);

$user = User::getUser($db, $id);
$user_reviews = User::getUserReviews($db, $id);
$categories = Category::getAllCategories($db);
drawCreateItem($user, $user_reviews, $categories, $session);

drawFooter();
?>
<script>
  const csrf = <?php echo json_encode($session->getSessionToken()) ?>;
  const categories = JSON.parse(JSON.stringify(<?php echo json_encode($categories); ?>));
</script>
<script src="./../javascript/utils.js"></script>
<script src="./../javascript/createItem.js"></script>