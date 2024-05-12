<?php
declare(strict_types=1);

require_once (__DIR__ . '/../database/item.class.php');
require_once (__DIR__ . '/../utils/session.php');
?>

<?php function drawCart(Session $session, array $cart)
{ ?>
  <!-- This is currently static - TODO make dinamic: -->
  <section id="cart">
    <?php if (empty($cart)): ?>
      <h2 id="empty-cart-title">O seu carrinho está vazio.</h2>
    <?php else: ?>
      <h2>Carrinho</h2>
      <div>
        <h3>Item</h3>
        <h3>Preço</h3>
        <h3>Envio</h3>
        <h3>Total</h3>
        <h3>Remover</h3>
      </div>
      <ol id="cart-items-container">
        <?php
        foreach ($cart as $cartItem) {
          drawCartItem($cartItem);
        }
        ?>
      </ol>
      <?php
      drawCartCheckout($session, $cart);
      ?>
    <?php endif; ?>
  </section>
<?php } ?>

<?php function drawCartItem(array $cartItem)
{ ?>
  <li data-cart-item="<?= $cartItem['item_id'] ?>" data-item-price="<?= $cartItem['new_price'] ?>"
    data-item-shipping="<?= $cartItem['shipping'] ?>">
    <!-- <div class="cart-item-img">
      <img src="https://ireland.apollo.olxcdn.com/v1/files/5inzf0kibmye2-PT/image;s=1000x700" alt="Item Image">
    </div> -->

    <div class="cart-item-info" onclick="handleItemBtn(<?= $cartItem['item_id'] ?>)">
      <h3><?= $cartItem['item_name'] ?></h3>
    </div>

    <div class="cart-item-price">

      <h3><?= $cartItem['new_price'] ?> €</h3>
      <?php if ($cartItem['old_price'] != $cartItem['new_price']): ?>
        <p><?= $cartItem['old_price'] ?> €</p>
      <?php endif; ?>
    </div>

    <div class="cart-item-shipment-price">
      <h3><?= $cartItem['shipping'] ?> €</h3>
    </div>

    <div class="cart-item-total-price">
      <h3><?= $cartItem['shipping'] + $cartItem['new_price'] ?> €</h3>
    </div>

    <button><ion-icon name="close-circle-outline"></ion-icon></button>
  </li>
<?php } ?>

<?php function drawCartCheckout(Session $session, array $cart)
{ ?>
  <?php
  $totalPrice = 0;
  $totalShipping = 0;
  foreach ($cart as $cartItem) {
    $totalPrice += $cartItem['new_price'];
    $totalShipping += $cartItem['shipping'];
  }
  ?>
  <footer id="cart-checkout">
    <div>
      <div id="cart-items-total-price">
        <p>Custo: </p>
        <h4><?= $totalPrice ?> €</h4>
      </div>

      <div id="cart-total-shipping-price">
        <p>Custo de envio: </p>
        <h4><?= $totalShipping ?> €</h4>
      </div>

      <div id="cart-total-price">
        <h3>Custo total: </h3>
        <h2><?= $totalPrice + $totalShipping ?> €</h2>
      </div>
      <button>Checkout</button>
    </div>
  </footer>
<?php } ?>