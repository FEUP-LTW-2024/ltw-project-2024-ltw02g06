<?php declare(strict_types = 1); ?>

<?php function drawUserLoginForm() { ?>
    <!DOCTYPE html>
    <html lang="pt">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>eKo</title>

            <link rel="stylesheet" href="../css/auth.css">
        </head>

        <body>
            <header>
                <a href="../pages/login.php">Entrar</a>
                <a href="../pages/register.php" id="unselected">Criar Conta</a>
            </header>
            <form action="../actions/action_login.php<?= isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : '' ?>" method="post" class="login">
                <a>Email</a>
                <input type="email" name="email">
                <a>Password</a>
                <input type="password" name="password">
                <a href="#">Esquececte-te da password?</a>
                <button type="submit">Entrar</button>
            </form>
        </body>
    </html>
<?php } ?>

<?php function drawUserRegisterForm() { ?>
    <!DOCTYPE html>
    <html lang="pt">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>eKo</title>

            <link rel="stylesheet" href="../css/auth.css">
        </head>

        <body>
            <header>
                <a href="../pages/login.php" id="unselected">Entrar</a>
                <a href="../pages/register.php">Criar Conta</a>
            </header>
            <form action="../actions/action_login.php<?= isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : '' ?>" method="post" class="login">
                <a>Email</a>
                <input type="email" name="email">
                <a>Password</a>
                <input type="password" name="password">
                <a href="#">Esquececte-te da password?</a>
                <button type="submit">Criar Conta</button>
            </form>
        </body>
    </html>
<?php } ?>