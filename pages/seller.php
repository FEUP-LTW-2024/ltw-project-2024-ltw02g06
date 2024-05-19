<?php
declare(strict_types=1);

require_once (__DIR__ . '/../utils/session.php');
$session = new Session();

$id = $session->getId();

if (!$id) {
  $redirectUrl = urlencode("/pages/seller.php");
  header("Location: login.php?redirect=$redirectUrl");
  exit();
}

require_once (__DIR__ . '/../database/connection.db.php');
require_once (__DIR__ . '/../database/item.class.php');

require_once (__DIR__ . '/../templates/header.tpl.php');
require_once (__DIR__ . '/../templates/footer.tpl.php');
require_once (__DIR__ . '/../templates/seller.tpl.php');
require_once (__DIR__ . '/../templates/item.tpl.php');

$db = getDatabaseConnection();

$items = Item::getAllItems($db, $id, $id, 1, PHP_INT_MAX, [], false);

$revenue = 0;
$sold = 0;
$toSend = 0;
$active = 0;

foreach ($items as $item) {
  if ($item['item']->status == 'sold') {
    $sold++;
    $revenue += $item['item']->soldPrice;
  } else if ($item['item']->status == 'to send') {
    $toSend++;
  } else if ($item['item']->status == 'active') {
    $active++;
  }
}

$search = isset($_GET['search']) ? $_GET['search'] : null;
$order = isset($search['order']) ? $search['order'] : 'relevance:desc';

drawHeader($session);
drawSellerDashboardAnalytics($session, $revenue, $sold, $toSend, $active);
drawSellerDashboardItems($session, $order);
drawFooter();
?>
<script>
  const csrf = <?php echo json_encode($session->getSessionToken()) ?>;
  const userId = <?php echo json_encode($session->getId()); ?>;
</script>
<script src="./../javascript/utils.js"></script>
<script src="./../javascript/seller.js"></script>