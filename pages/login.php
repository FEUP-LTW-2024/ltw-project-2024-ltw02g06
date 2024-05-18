<?php
declare(strict_types=1);

require_once (__DIR__ . '/../utils/session.php');
$session = new Session();
$id = $session->getId();

if ($id) {
  header('Location: ' . '../pages/items.php');
  exit();
}

require_once (__DIR__ . '/../database/connection.db.php');

require_once (__DIR__ . '/../templates/header.tpl.php');
require_once (__DIR__ . '/../templates/footer.tpl.php');

$db = getDatabaseConnection();

drawHeader($session);
?>

<section id="login-section">
  <form id="login-form">
    <label for="email">Email</label>
    <input type="email" name="email" placeholder="Email">
    <label for="password">Password</label>
    <input type="password" name="password" placeholder="Password">
    <button type="submit">Login</button>
    <a href="../pages/register.php">Não possui uma conta?</a>
  </form>
</section>

<?php
drawFooter();
?>
<script>
  const redirectURL = <?php echo json_encode(htmlspecialchars($_GET['redirect'] ?? "../pages/items.php", ENT_QUOTES, 'UTF-8')) ?>;
</script>
<script src="./../javascript/utils.js"></script>
<script src="./../javascript/login.js"></script>