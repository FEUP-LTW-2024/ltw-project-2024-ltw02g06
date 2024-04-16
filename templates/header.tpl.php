<?php
declare(strict_types=1);

require_once (__DIR__ . '/../utils/session.php');
?>

<?php function drawHeader(Session $session)
{ ?>
  <!DOCTYPE html>
  <html lang="pt">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eKo</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
      href="https://fonts.googleapis.com/css2?family=Karla:ital,wght@0,200..800;1,200..800&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
      rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/search-bar.css">
    <link rel="stylesheet" href="../css/breadcrumb-nav.css">
    <link rel="stylesheet" href="../css/filters.css">
    <link rel="stylesheet" href="../css/item.css">
    <link rel="stylesheet" href="../css/items.css">
    <link rel="stylesheet" href="../css/profile.css">
    <link rel="stylesheet" href="../css/inbox.css">
    <link rel="stylesheet" href="../css/cart.css">
  </head>

  <body>

    <header id="header">
      <div>
        <h1>eKo</h1>
        <nav>
          <ul>
            <li><a href="#"><ion-icon name="chatbox-outline"></ion-icon></a></li>
            <li><a href="#"><ion-icon name="heart-outline"></ion-icon></a></li>
            <li><a href="#"><ion-icon name="cart-outline"></ion-icon></a></li>
            <li><a href="#"><ion-icon name="person-outline"></ion-icon></a></li>
            <li><a href="#">Anunciar e Vender</a></li>
          </ul>
        </nav>
      </div>
    </header>

    <main>
    <?php } ?>