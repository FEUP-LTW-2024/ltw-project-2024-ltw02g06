<?php
declare(strict_types=1);

require_once (__DIR__ . '/../utils/session.php');
$session = new Session();

require_once (__DIR__ . '/../database/connection.db.php');
require_once (__DIR__ . '/../database/item.class.php');

require_once (__DIR__ . '/../templates/header.tpl.php');
require_once (__DIR__ . '/../templates/footer.tpl.php');
require_once (__DIR__ . '/../templates/search-bar.tpl.php');
require_once (__DIR__ . '/../templates/filters.tpl.php');
require_once (__DIR__ . '/../templates/item.tpl.php');

$db = getDatabaseConnection();

// $items = Item::getItem($db, intval($_GET['id']));
// create function to get all items depending on the filters

drawHeader($session);
drawSearchBar();
drawFilters();
drawItems($session);
drawFooter();
?>
<script src="./../javascript/searchItem.js"></script>