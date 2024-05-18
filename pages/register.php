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
<section id="register-section">
  <form id="register-form">
    <label for="email">Email</label>
    <input type="email" name="email" placeholder="Email">
    <label for="password">Password</label>
    <input type="password" name="password" placeholder="Password">
    <label for="confirmPassword">Confirmar Password</label>
    <input type="password" name="confirmPassword" placeholder="Password">
    <label for="firstName">Primeiro nome</label>
    <input type="text" name="firstName" placeholder="Primeiro nome">
    <label for="lastName">Sobrenome</label>
    <input type="text" name="lastName" placeholder="Sobrenome">
    <label for="address">Morada</label>
    <input type="text" name="address" placeholder="Morada">
    <label for="zipcode">Código postal</label>
    <input type="text" name="zipcode" placeholder="Código postal">
    <label for="city">Cidade</label>
    <input type="text" name="city" placeholder="Cidade">
    <label for="state">Distrito</label>
    <input type="text" name="state" placeholder="Distrito">
    <label for="country">País</label>
    <input type="text" name="country" placeholder="País">
    <button type="submit">Registrar</button>
    <a href="../pages/login.php">Já possui uma conta?</a>
  </form>
</section>

<?php
drawFooter();
?>

<script>
  const redirectURL = <?php echo json_encode(htmlspecialchars($_GET['redirect'] ?? "../pages/items.php", ENT_QUOTES, 'UTF-8')) ?>;
</script>
<script src="./../javascript/utils.js"></script>
<script src="./../javascript/register.js"></script>