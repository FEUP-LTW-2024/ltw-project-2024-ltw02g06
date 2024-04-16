<?php
declare(strict_types=1);

require_once (__DIR__ . '/../database/item.class.php');
require_once (__DIR__ . '/../utils/session.php');
?>

<?php function drawCart(Session $session)
{ ?>
  <!-- This is currently static - TODO make dinamic: -->
  <section id="cart">
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
      drawCartItem();
      drawCartItem();
      ?>
    </ol>
    <?php
    drawCartCheckout();
    ?>
  </section>
<?php } ?>

<?php function drawCartItem()
{ ?>
  <li>
    <div class="cart-item-img">
      <img src="https://ireland.apollo.olxcdn.com/v1/files/5inzf0kibmye2-PT/image;s=1000x700" alt="Item Image">
    </div>

    <div class="cart-item-info">
      <h3>Lexus GS 450H - </h3>
    </div>

    <div class="cart-item-price">
      <h3>15.990 €</h3>
      <p>17.000 €</p>
    </div>

    <div class="cart-item-shipment-price">
      <h3>200 €</h3>
    </div>

    <div class="cart-item-total-price">
      <h3>16.190 €</h3>
    </div>

    <button><ion-icon name="close-circle-outline"></ion-icon></button>
  </li>
<?php } ?>

<?php function drawCartCheckout()
{ ?>
  <footer id="cart-checkout">
    <div>
      <div id="cart-items-total-price">
        <p>Custo: </p>
        <h4>31.880 €</h4>
      </div>

      <div id="cart-total-shipping-price">
        <p>Custo de envio: </p>
        <h4>400 €</h4>
      </div>

      <div id="cart-total-price">
        <h3>Custo total: </h3>
        <h2>32.280 €</h2>
      </div>
      <button>Checkout</button>
    </div>
  </footer>
<?php } ?>