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

$search = isset($_GET['search']) ? $_GET['search'] : null;
$order = isset($search['order']) ? $search['order'] : 'relevance:desc';
$selectedCategory = isset($search['category']) ? (int) $search['category'] : null;
$selectedCategory = $selectedCategory >= 1 ? $selectedCategory : null;
$attributes = isset($search['attributes']) ? $search['attributes'] : [];
$categories = Category::getAllCategories($db);

drawHeader($session);
drawSearchBar();
drawFilters($categories, $selectedCategory, $attributes);
drawItems($session, $order);
drawFooter();
?>
<script>
  const categories = JSON.parse(JSON.stringify(<?php echo json_encode($categories); ?>));
</script>
<script src="./../javascript/utils.js"></script>
<script src="./../javascript/searchItem.js"></script>