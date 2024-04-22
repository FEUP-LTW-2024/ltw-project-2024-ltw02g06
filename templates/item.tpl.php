<?php
declare(strict_types=1);

require_once (__DIR__ . '/../database/item.class.php');
require_once (__DIR__ . '/../utils/session.php');

require_once (__DIR__ . '/../templates/search-bar.tpl.php');
?>

<?php function drawItem(Item $item, User $seller, array $seller_reviews, bool $is_item_in_wishlist, Session $session)
{ ?>

  <?php
  $is_seller = $session->getId() == $seller->id;
  ?>

  <article id="item">

    <div id="item-image-container">
      <div>
        <?php if (empty($item->images)): ?>
          <div>
            <h2>Este anúncio não possui imagens.</h2>
          </div>
        <?php else: ?>
          <?php foreach ($item->images as $index => $image): ?>
            <img src="<?= '/../' . $image['path'] ?>" alt="Item Image" <?= $index === 0 ? '' : 'style="display: none;"' ?>>
          <?php endforeach; ?>
        <?php endif; ?>
        <button id="previous-image-btn"><ion-icon name="chevron-back"></ion-icon></button>
        <button id="next-image-btn"><ion-icon name="chevron-forward"></ion-icon></button>
      </div>
    </div>

    <div id="item-description-container">
      <h2>
        Descrição
      </h2>

      <?php if (trim($item->description) === ""): ?>
        <p>Sem descrição.</p>
      <?php else: ?>
        <p><?= $item->description ?></p>
      <?php endif; ?>

      <ul id="item-category-list">
        <?php foreach ($item->attributes as $attribute): ?>
          <li>
            <p><?= $attribute['name'] ?>: <?= $attribute['value'] ?></p>
          </li>
        <?php endforeach; ?>
      </ul>
      <div>
        <p>ID: <?= $item->id ?></p>
        <p>Cliques: <?= $item->clicks ?></p>
        <button id="report-button">
          Reportar
        </button>
      </div>
    </div>

    <div id="item-info">
      <div>
        <p class="small-font-size">Publicado
          <?= $item->creation_date->format('d/m/Y'); ?>
        </p>
        <?php if (!$is_seller): ?>
          <button id="whishlist-btn" data-is-item-in-wishlist=<?= $is_item_in_wishlist ? "1" : "0" ?>><ion-icon
              name=<?= $is_item_in_wishlist ? "heart" : "heart-outline" ?>></ion-icon></button>
        <?php endif; ?>
      </div>
      <h3 id="item-name"><?= $item->name ?></h3>
      <h2 id="item-price"><?= $item->price ?> €</h2>
      <?php if (!$is_seller): ?>
        <button id="add-to-cart-btn">Adicionar ao carrinho</button>
        <button id="negotiate-btn">Propor outro preço</button>
        <button id="send-message-btn">Enviar mensagem</button>
      <?php else: ?>
        <button id="edit-btn">Editar anúncio <ion-icon name="create-outline"></ion-icon></button>
        <button id="delete-btn">Apagar anúncio <ion-icon name="trash-outline"></ion-icon></button>
      <?php endif; ?>
    </div>

    <div id="item-location">
      <h3>Localização</h3>
      <div>
        <div>
          <h4><?= $seller->city ?></h4>
          <p><?= $seller->state . ', ' . $seller->country ?></p>
        </div>
        <a href="https://www.google.com/maps/search/?api=1&query=<?= urlencode($seller->city . ', ' . $seller->state . ', ' . $seller->country) ?>"
          target="_blank">
          <img src="https://www.olx.pt/app/static/media/staticmap.65e20ad98.svg" alt="Location Map">
        </a>
      </div>
    </div>

    <div id="seller-info">
      <h3>Utilizador</h3>
      <a href="/pages/profile.php?id=<?php echo $seller->id; ?>">
        <img id="seller-img" src="<?= '/../' . $seller->image ?>" alt="User Profile Picture">
        <div>
          <h4 id="seller-name"><?= $seller->name() ?></h4>
          <p class="small-font-size">
            No eKo desde
            <?= $seller->registration_date->format('d/m/Y'); ?>
          </p>
          <!-- <p class="small-font-size">Esteve online dia 07 de abril de 2024</p> -->
        </div>
      </a>
      <div>
        <?php if (empty($seller_reviews)): ?>
          <h6>Rating: </h6>
          <p>Sem classificações</p>
        <?php else: ?>
          <?php
          $total_rating = 0;
          foreach ($seller_reviews as $review) {
            $total_rating += $review['rating'];
          }

          $average_rating = count($seller_reviews) > 0 ? $total_rating / count($seller_reviews) : 0;
          ?>
          <h6>Rating <?= number_format($average_rating, 1) ?>/5</h6>
          <p class="small-font-size">17 classificações</p>
        <?php endif; ?>
      </div>
      <a id="see-all-items-btn" href="/pages/profile.php?id=<?php echo $seller->id; ?>">
        <span>Todos os anúncios deste anunciante</span>
        <span>&gt;</span>
      </a>
    </div>

  </article>
<?php } ?>

<!-- TODO -->
<?php function drawEditItem()
{ ?>

<?php } ?>

<?php function drawItems(Session $session)
{ ?>
  <!-- This is currently static - TODO make dinamic:
    -> Get items depending on the filters from db; -->
  <section id="items">

    <header>
      <h2>Encontramos mais de 1000 anúncios</h2>
      <div>
        <p>Ordenar por:</p>
        <select name="items-order" id="items-order">
          <option value="1" selected>Anúncios recomendados</option>
          <option value="2">Mais barato</option>
          <option value="3">Mais caro</option>
          <option value="4">Mais recente</option>
          <option value="5">Mais antigo</option>
        </select>
      </div>
    </header>
    <ol id="items-container">
      <?php
      drawSearchPageItem($session);
      drawSearchPageItem($session);
      ?>
    </ol>

    <nav>
      <button><ion-icon name="chevron-back"></ion-icon></button>
      <button>2</button>
      <button class="selected-page">3</button>
      <button>4</button>
      <p>...</p>
      <button>25</button>
      <button><ion-icon name="chevron-forward"></ion-icon></button>
    </nav>

  </section>
<?php } ?>

<?php function drawSearchPageItem(Session $session)
{ ?>
  <li>
    <img src="https://ireland.apollo.olxcdn.com/v1/files/5inzf0kibmye2-PT/image;s=1000x700" alt="Item Image">
    <div>
      <div>
        <h3>Lexus GS 450H - Garantia - Nacional - Bastantes Extras - 345cv</h3>
        <div>
          <h3>15.990 €</h3>
          <p>Negociável</p>
        </div>
      </div>
      <div>
        <div>
          <h4>Custóias, Leça Do Balio E Guifões,</h4>
          <p>Porto</p>
        </div>
        <button><ion-icon name="heart-outline"></ion-icon></button>
      </div>
    </div>
  </li>
<?php } ?>

<?php function drawProfileItems(Session $session)
{ ?>
  <!-- This is currently static - TODO make dinamic:
    -> Get items depending on the user profile from db; -->
  <section id="items">
    <h2>Os meus anúncios:</h2>
    <header>
      <?php drawSmallSearchBar() ?>
      <div>
        <p>Ordenar por:</p>
        <select name="items-order" id="items-order">
          <option value="1" selected>Anúncios recomendados</option>
          <option value="2">Mais barato</option>
          <option value="3">Mais caro</option>
          <option value="4">Mais recente</option>
          <option value="5">Mais antigo</option>
        </select>
      </div>
    </header>

    <ol id="items-container">
      <?php
      drawProfileItem($session);
      drawProfileItem($session);
      ?>
    </ol>

    <nav>
      <button><ion-icon name="chevron-back"></ion-icon></button>
      <button>2</button>
      <button class="selected-page">3</button>
      <button>4</button>
      <p>...</p>
      <button>25</button>
      <button><ion-icon name="chevron-forward"></ion-icon></button>
    </nav>

  </section>
<?php } ?>

<?php function drawProfileItem(Session $session)
{ ?>
  <li>
    <img src="https://ireland.apollo.olxcdn.com/v1/files/5inzf0kibmye2-PT/image;s=1000x700" alt="Item Image">
    <div>
      <div>
        <h3>Lexus GS 450H - Garantia - Nacional - Bastantes Extras - 345cv</h3>
        <div>
          <h3>15.990 €</h3>
          <p>Negociável</p>
        </div>
      </div>
      <div>
        <div>
          <h4>Custóias, Leça Do Balio E Guifões,</h4>
          <p>Porto</p>
        </div>
        <button><ion-icon name="heart-outline"></ion-icon></button>
      </div>
    </div>
  </li>
<?php } ?>

<?php function drawWishlistItems(Session $session)
{ ?>
  <section id="items" class="wishlist">
    <h2>A minha whishlist:</h2>
    <header>
      <?php drawSmallSearchBar() ?>
      <div>
        <p>Ordenar por:</p>
        <select name="items-order" id="items-order">
          <option value="1" selected>Anúncios recomendados</option>
          <option value="2">Mais barato</option>
          <option value="3">Mais caro</option>
          <option value="4">Mais recente</option>
          <option value="5">Mais antigo</option>
        </select>
      </div>
    </header>
    <ol id="items-container">
      <?php
      drawWishlistItem($session);
      drawWishlistItem($session);
      ?>
    </ol>

    <nav>
      <button><ion-icon name="chevron-back"></ion-icon></button>
      <button>2</button>
      <button class="selected-page">3</button>
      <button>4</button>
      <p>...</p>
      <button>25</button>
      <button><ion-icon name="chevron-forward"></ion-icon></button>
    </nav>

  </section>
<?php } ?>

<?php function drawWishlistItem(Session $session)
{ ?>
  <li>
    <img src="https://ireland.apollo.olxcdn.com/v1/files/5inzf0kibmye2-PT/image;s=1000x700" alt="Item Image">
    <div>
      <div>
        <h3>Lexus GS 450H - Garantia - Nacional - Bastantes Extras - 345cv</h3>
        <div>
          <h3>15.990 €</h3>
          <p>Negociável</p>
        </div>
      </div>
      <div>
        <div>
          <h4>Custóias, Leça Do Balio E Guifões,</h4>
          <p>Porto</p>
        </div>
        <div>
          <button><ion-icon name="heart-outline"></ion-icon></button>
          <button><ion-icon name="cart-outline"></ion-icon></button>
        </div>
      </div>
    </div>
  </li>
<?php } ?>

<?php function drawSellerDashboardActiveItem(Session $session)
{ ?>
  <li>
    <div>
      <h3 title="Lexus GS 450H - Garantia - Nacional - Bastantes Extras - 345cv">Lexus GS 450H - Garantia -
        Nacional - Bastantes Extras - 345cv</h3>
      <h3>15.990 €</h3>
    </div>
    <div>
      <button title="Promover anúncio"><ion-icon name="star-outline"></ion-icon></button>
      <button title="Editar anúncio"><ion-icon name="create-outline"></ion-icon></button>
    </div>
  </li>
<?php } ?>

<?php function drawSellerDashboardToSendItem(Session $session)
{ ?>
  <li>
    <div>
      <h3 title="Lexus GS 450H - Garantia - Nacional - Bastantes Extras - 345cv">Lexus GS 450H - Garantia -
        Nacional - Bastantes Extras - 345cv</h3>
      <h3>15.990 €</h3>
    </div>
    <div>
      <button title="Enviar"><ion-icon name="send-outline"></ion-icon></button>
    </div>
  </li>
<?php } ?>

<?php function drawSellerDashboardSoldItem(Session $session)
{ ?>
  <li>
    <div>
      <h3 title="Lexus GS 450H - Garantia - Nacional - Bastantes Extras - 345cv">Lexus GS 450H - Garantia -
        Nacional - Bastantes Extras - 345cv</h3>
      <h3>15.990 €</h3>
    </div>
    <div>
      <button title="Apagar"><ion-icon name="trash-outline"></ion-icon></button>
    </div>
  </li>
<?php } ?>

<?php function drawSellerDashboardItems(Session $session)
{ ?>
  <section id="seller-dashboard-items-section">
    <h2>Os meus anúncios:</h2>
    <header>
      <?php
      drawSmallSearchBar();
      ?>
      <div>
        <p>Ordenar por:</p>
        <select name="items-order" id="items-order">
          <option value="1" selected>Mais recente</option>
          <option value="2">Mais antigo</option>
          <option value="4">Mais barato</option>
          <option value="5">Mais caro</option>
          <option value="3">Anúncios promovidos</option>
        </select>
      </div>
    </header>

    <div id="seller-dashboard-items">
      <div>
        <h3>Por vender:</h3>
        <ol class="seller-dashboard-items-container">
          <?php
          drawSellerDashboardActiveItem($session);
          drawSellerDashboardActiveItem($session);
          drawSellerDashboardActiveItem($session);
          drawSellerDashboardActiveItem($session);
          drawSellerDashboardActiveItem($session);
          drawSellerDashboardActiveItem($session);
          ?>
        </ol>
      </div>

      <div>
        <h3>Por enviar:</h3>
        <ol class="seller-dashboard-items-container">
          <?php
          drawSellerDashboardToSendItem($session);
          drawSellerDashboardToSendItem($session);
          drawSellerDashboardToSendItem($session);
          drawSellerDashboardToSendItem($session);
          drawSellerDashboardToSendItem($session);
          drawSellerDashboardToSendItem($session);
          ?>
        </ol>
      </div>

      <div>
        <h3>Vendidos:</h3>
        <ol class="seller-dashboard-items-container">
          <?php
          drawSellerDashboardSoldItem($session);
          drawSellerDashboardSoldItem($session);
          drawSellerDashboardSoldItem($session);
          drawSellerDashboardSoldItem($session);
          drawSellerDashboardSoldItem($session);
          drawSellerDashboardSoldItem($session);
          ?>
        </ol>
      </div>
    </div>
  </section>
<?php } ?>


<?php function drawAdminReportedItems()
{ ?>
  <ul id="reported-items-container">
    <?php
    drawAdminReportedItem();
    drawAdminReportedItem();
    drawAdminReportedItem();
    drawAdminReportedItem();
    ?>
  </ul>
<?php } ?>

<?php function drawAdminReportedItem()
{ ?>
  <li>
    <div class="reported-item">
      <h3>Lexus GS 450H - Garantia - Nacional - Bastantes Extras - 345cv</h3>
      <div>
        <button title="Manter anúncio"><ion-icon name="checkmark-circle-outline"></ion-icon></button>
        <button title="Apagar anúncio"><ion-icon name="trash-outline"></ion-icon></button>
      </div>
    </div>

    <div class="report-item-msg">
      <div>
        <h4>Luís Figo</h4>
        <span>há 3 min</span>
      </div>
      <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Ipsa doloribus vel quis adipisci non vitae eius
        saepe.</p>
    </div>

  </li>
<?php } ?>