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

$db = getDatabaseConnection();

$search = isset($_GET['search']['search']) ? trim($_GET['search']['search']) : null;
if ($search == "")
  $search = null;

$inbox = Message::getInbox($db, $id, $search);

drawHeader($session);
drawSmallSearchBar();
drawInbox($session, $inbox);
drawFooter();
?>
<script>
  const csrf = <?php echo json_encode($session->getSessionToken()) ?>;
  const userId = <?php echo json_encode($session->getId()); ?>;
</script>
<script src="./../javascript/utils.js"></script>
<script src="./../javascript/inbox.js"></script>