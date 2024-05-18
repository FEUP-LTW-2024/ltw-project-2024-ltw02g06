<?php
declare(strict_types=1);

require_once (__DIR__ . '/../utils/session.php');
require_once (__DIR__ . '/../database/connection.db.php');
require_once (__DIR__ . '/../database/user.class.php');
?>

<?php function drawHeader(Session $session)
{ ?>
  <?php
  $id = $session->getId();
  $db = getDatabaseConnection();
  $user = null;
  if ($id)
    $user = User::getUser($db, $id);
  ?>
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
    <script src="https://cdn.jsdelivr.net/npm/uuid@8.3.2/dist/umd/uuidv4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.2/jspdf.min.js"></script>

    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/search-bar.css">
    <link rel="stylesheet" href="../css/breadcrumb-nav.css">
    <link rel="stylesheet" href="../css/filters.css">
    <link rel="stylesheet" href="../css/item.css">
    <link rel="stylesheet" href="../css/items.css">
    <link rel="stylesheet" href="../css/profile.css">
    <link rel="stylesheet" href="../css/inbox.css">
    <link rel="stylesheet" href="../css/cart.css">
    <link rel="stylesheet" href="../css/seller.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/editItem.css">
    <link rel="stylesheet" href="../css/editProfile.css">
    <link rel="stylesheet" href="../css/chat.css">
    <link rel="stylesheet" href="../css/category.css">
    <link rel="stylesheet" href="../css/auth.css">
  </head>

  <body>

    <header id="header">
      <div>
        <a href="../pages/items.php">
          <h1>eKo</h1>
        </a>
        <nav>
          <ul>

            <li title="Inbox"><a href="../pages/inbox.php"><ion-icon name="chatbox-outline"></ion-icon></a></li>
            <li title="Wishlist"><a href="../pages/wishlist.php"><ion-icon name="heart-outline"></ion-icon></a></li>
            <li title="Carrinho"><a href="../pages/cart.php"><ion-icon name="cart-outline"></ion-icon></a></li>
            <?php if ($user && $user->admin): ?>
              <li title="Admin"><a href="../pages/admin.php"><ion-icon name="star-outline"></ion-icon></a></li>
            <?php endif; ?>
            <li title="Vendedor"><a href="../pages/seller.php"><ion-icon name="storefront-outline"></ion-icon></a></li>
            <?php if ($id): ?>
              <li title="Perfil"><a href="../pages/profile.php"><ion-icon name="person-outline"></ion-icon></a></li>
              <li title="Log out"><a href="../actions/action_logout.php"><ion-icon name="exit-outline"></ion-icon></a></li>
            <?php else: ?>
              <li title="Log in">
                <a href="../pages/login.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>"><ion-icon
                    name="person-outline"></ion-icon></a>
              </li>
            <?php endif; ?>
          </ul>
        </nav>
      </div>
    </header>

    <main>
      <div id="modal">
        <div class="modal-content">
          <p id="modal-message"></p>
          <button><ion-icon class="close" name="close-circle-outline"><ion-icon></button>
        </div>
      </div>
    <?php } ?>