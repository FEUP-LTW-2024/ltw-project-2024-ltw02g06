<?php
declare(strict_types=1);

require_once (__DIR__ . '/../utils/session.php');
$session = new Session();
$id = $session->getId();

if (!$id) {
  $redirectUrl = urlencode('/pages/category.php?id=' . $_GET['id']);
  header("Location: ../pages/login.php?redirect=$redirectUrl");
  exit();
}

require_once (__DIR__ . '/../database/connection.db.php');
require_once (__DIR__ . '/../database/category.class.php');
require_once (__DIR__ . '/../database/user.class.php');

require_once (__DIR__ . '/../templates/header.tpl.php');
require_once (__DIR__ . '/../templates/footer.tpl.php');
require_once (__DIR__ . '/../templates/category.tpl.php');

$db = getDatabaseConnection();

$user = User::getUser($db, $id);
$category = Category::getCategory($db, intval($_GET['id']));

drawHeader($session);

if (!$category || $category->id == 1 || !$user->admin) { ?>
  <h2>Ups! Página não encontrada. Por favor, verifique o URL e tente novamente.</h2>
  <?php
  drawFooter();
  exit();
} ?>
<?php
drawEditCategory($session, $category);
drawFooter();
?>
<script>
  const csrf = <?php echo json_encode($session->getSessionToken()) ?>;
  const categoryId = <?php echo json_encode($category->id); ?>;
  const category = <?php echo json_encode($category); ?>;
</script>
<script src="./../javascript/category.js"></script>