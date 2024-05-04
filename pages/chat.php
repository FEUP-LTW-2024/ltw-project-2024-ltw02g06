<?php
declare(strict_types=1);

require_once (__DIR__ . '/../utils/session.php');
$session = new Session();

$id = $session->getId();

if (!$id) {
  $redirectUrl = urlencode("/pages/inbox.php");
  header("Location: login.php?redirect=$redirectUrl");
  exit();
}

require_once (__DIR__ . '/../database/connection.db.php');

require_once (__DIR__ . '/../templates/header.tpl.php');
require_once (__DIR__ . '/../templates/footer.tpl.php');
require_once (__DIR__ . '/../templates/search-bar.tpl.php');
require_once (__DIR__ . '/../templates/inbox.tpl.php');

require_once (__DIR__ . '/../database/message.class.php');
require_once (__DIR__ . '/../database/user.class.php');

$db = getDatabaseConnection();

drawHeader($session);

$item = Item::getItem($db, intval($_GET['item']));
$other_user = User::getUser($db, intval($_GET['id']));
$seller = User::getUser($db, $item->seller);

if (!$other_user || !$item || !$item->status != 'active' || $other_user->id == $id): ?>
  <h2>Ups! Página não encontrada. Por favor, verifique o URL e tente novamente.</h2>
  <?php
  exit();
endif;

$chat = Message::getChat($db, $item->id, $id, $other_user->id);

if (empty($chat) && $item->seller == $id): ?>
  <h2>Ups! Página não encontrada. Por favor, verifique o URL e tente novamente.</h2>
  <?php
  exit();
endif;

$cart_item = User::getCartItem($db, $id, $item->id);
drawChat($session, $chat, $other_user->id, $item, $seller);
drawFooter();
?>
<script>
  const itemId = <?php echo json_encode($item->id); ?>;
  const userId = <?php echo json_encode($id); ?>;
  const otherUserId = <?php echo json_encode($other_user->id); ?>;
  const otherUserFirstName = <?php echo json_encode($other_user->first_name); ?>;
  var cartItemPrice = <?php echo json_encode(intval($cart_item["price"]) ?? null); ?>;
</script>
<script src="./../javascript/utils.js"></script>
<script src="./../javascript/chat.js"></script>