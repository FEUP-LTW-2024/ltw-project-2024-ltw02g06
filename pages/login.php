<?php
declare(strict_types=1);

require_once (__DIR__ . '/../utils/session.php');
$session = new Session();

require_once (__DIR__ . '/../database/connection.db.php');

require_once (__DIR__ . '/../templates/header.tpl.php');
require_once (__DIR__ . '/../templates/footer.tpl.php');

$db = getDatabaseConnection();

drawHeader($session);
?>
<form
  action="../actions/action_login.php<?= isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : '' ?>"
  method="post" class="login">
  <input type="email" name="email" placeholder="email">
  <input type="password" name="password" placeholder="password">
  <a href="../pages/register.php">Register</a>
  <button type="submit">Login</button>
</form>
<section id="messages">
  <?php foreach ($session->getMessages() as $messsage) { ?>
    <article class="<?= $messsage['type'] ?>">
      <?= $messsage['text'] ?>
    </article>
  <?php } ?>
</section>
<?php
drawFooter();
?>