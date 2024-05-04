<?php
declare(strict_types=1);

require_once (__DIR__ . '/../database/item.class.php');
require_once (__DIR__ . '/../database/category.class.php');
require_once (__DIR__ . '/../utils/session.php');

require_once (__DIR__ . '/../templates/search-bar.tpl.php');
?>

<?php function drawItem(Item $item, User $seller, array $seller_reviews, bool $is_item_in_wishlist, bool $is_item_in_cart, Session $session)
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
        <p><?= nl2br(trim($item->description)) ?></p>
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
      <?php if (trim($item->description) === ""): ?>
        <h3 id="item-name">Sem nome</h3>
      <?php else: ?>
        <h3 id="item-name"><?= trim($item->name) ?></h3>
      <?php endif; ?>
      <h2 id="item-price"><?= $item->price ?> €</h2>
      <?php if (!$is_seller): ?>
        <button id="add-to-cart-btn" data-is-item-in-cart=<?= $is_item_in_cart ? "1" : "0" ?>>
          <?= $is_item_in_cart ? "Remover do carrinho" : "Adicionar ao carrinho" ?>
        </button>
        <button id="negotiate-btn">Propor outro preço</button>
        <button id="send-message-btn">Enviar mensagem</button>
      <?php else: ?>
        <button id="edit-btn">Editar anúncio <ion-icon name="create-outline"></ion-icon></button>
        <button id="delete-btn">Apagar anúncio <ion-icon name="trash-outline"></ion-icon></button>
      <?php endif; ?>
    </div>

    <div id="item-location" class="edit-item-location">
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

    <div id="seller-info" class="edit-seller-info">
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
          <p class="small-font-size"><?= count($seller_reviews) ?> classificações</p>
        <?php endif; ?>
      </div>
      <a id="see-all-items-btn" href="/pages/profile.php?id=<?php echo $seller->id; ?>">
        <span>Todos os anúncios deste anunciante</span>
        <span>&gt;</span>
      </a>
    </div>

  </article>
<?php } ?>

<?php function drawEditItem(Item $item, User $user, array $user_reviews, array $categories, Session $session)
{ ?>
  <form id="edit-item"
    action="../actions/action_edit_item.php<?= isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : '?redirect=' . urlencode('/pages/item.php?id=' . $item->id) ?>"
    method="post">
    <input type="hidden" name="item_id" value=<?= $item->id ?>>
    <div id="edit-item-buttons">
      <button id="edit-item-cancel-btn" type="button">Cancelar<ion-icon name="close"></ion-icon></button>
      <button id="edit-item-submit-btn" type="submit">Confirmar<ion-icon name="checkmark" submit></ion-icon></button>
    </div>
    <div id="edit-item-image-container">
      <?php foreach ($item->images as $image): ?>
        <div>
          <img src="<?= '/../' . $image['path'] ?>" alt="Item Image">
          <button onclick="removeImage(this)" type="button"><ion-icon name="trash-outline"></ion-icon></button>
          <input type="hidden" name="images[<?= $image['id'] ?>]" value="<?= $image['path'] ?>">
        </div>
      <?php endforeach; ?>
      <div id="new-image-container">
        <div id="preview-new-image"></div>
        <input onchange="previewImage(event)" type="file" name="new-image-input" id="new-image-input" accept="image/*">
        <label for="new-image-input"><ion-icon name="add"></ion-icon></label>
        <div style="display: none;">
          <button onclick="closePreview()" id="close-preview-image-btn" type="button"><ion-icon
              name="close"></ion-icon></button>
          <button onclick="acceptPreview()" id="accept-preview-image-btn" type="button"><ion-icon
              name="checkmark"></ion-icon></button>
        </div>
      </div>
    </div>

    <div id="edit-item-description-container">
      <h2>
        Descrição
      </h2>
      <textarea id="edit-item-description" name="item_description" rows="10"
        placeholder="Descrição do anúncio"><?= $item->description ?></textarea>
      <ol id="edit-item-category-list">
        <li>
          <label for="category">Categoria</label>
          <select name="category" id="category">
            <?php foreach ($categories as $category): ?>
              <option value=<?= $category->id ?>     <?= $category->id == $item->category ? "selected" : "" ?>><?= $category->name ?></option>
            <?php endforeach; ?>
          </select>
        </li>
        <?php foreach ($categories[$item->category]->attributes as $attribute): ?>
          <li>
            <label for="<?= $attribute["name"] ?>"><?= $attribute["name"] ?></label>

            <?php if ($attribute["type"] == "real"): // TODO Change this input later ?>
              <input type="text" name="attributes[<?= $attribute['id'] ?>]" id=<?= $attribute["name"] ?>>

            <?php elseif ($attribute["type"] == "default"): ?>
              <input type="text" name="attributes[<?= $attribute['id'] ?>]" id=<?= $attribute["name"] ?>>

            <?php elseif ($attribute["type"] == "enum"): ?>
              <select name="attributes[<?= $attribute['id'] ?>]" id=<?= $attribute["name"] ?>>
                <?php foreach ($attribute["values"] as $value): ?>
                  <option value=<?= $value["value"] ?>         <?= $item->attributes[$attribute['id']]['value'] == $value["value"] ? "selected" : "" ?>>
                    <?= $value["value"] ?>
                  </option>
                <?php endforeach; ?>
              </select>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ol>
    </div>

    <div id="edit-item-info">
      <p class="small-font-size">Publicado
        <?= $item->creation_date->format('d/m/Y'); ?>
      </p>
      <input type="text" name="item_name" id="edit-item-name" placeholder="Nome do anúncio" value=<?= $item->name ?>>
      <span>
        <input type="text" name="item_price" id="edit-item-price" placeholder="Preço" value=<?= $item->price ?>>
        €
      </span>
      <button id="add-to-cart-btn" class="deactivated-btn" type="button">Adicionar ao carrinho</button>
      <button id="negotiate-btn" class="deactivated-btn" type="button">Propor outro preço</button>
      <button id="send-message-btn" class="deactivated-btn" type="button">Enviar mensagem</button>
    </div>

    <div id="item-location" class="edit-item-deactivated">
      <h3>Localização</h3>
      <div>
        <div>
          <h4><?= $user->city ?></h4>
          <p><?= $user->state . ', ' . $user->country ?></p>
        </div>
        <img src="https://www.olx.pt/app/static/media/staticmap.65e20ad98.svg" alt="Location Map">
      </div>
    </div>

    <div id="seller-info">
      <h3>Utilizador</h3>
      <div>
        <img id="seller-img" src="<?= '/../' . $user->image ?>" alt="User Profile Picture">
        <div>
          <h4 id="seller-name"><?= $user->name() ?></h4>
          <p class="small-font-size">
            No eKo desde
            <?= $user->registration_date->format('d/m/Y'); ?>
          </p>
          <!-- <p class="small-font-size">Esteve online dia 07 de abril de 2024</p> -->
        </div>
      </div>
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
          <p class="small-font-size"><?= count($seller_reviews) ?> classificações</p>
        <?php endif; ?>
      </div>
    </div>

  </form>
<?php } ?>

<?php function drawCreateItem(User $user, array $user_reviews, array $categories, Session $session)
{ ?>
  <form id="edit-item" action="../actions/action_create_item.php" method="post">
    <div id="edit-item-buttons">
      <h2>Novo Anúncio:</h2>
      <button id="edit-item-cancel-btn" type="button">Cancelar<ion-icon name="close"></ion-icon></button>
      <button id="edit-item-submit-btn" type="submit">Confirmar<ion-icon name="checkmark" submit></ion-icon></button>
    </div>
    <div id="edit-item-image-container">
      <div id="new-image-container">
        <div id="preview-new-image"></div>
        <input onchange="previewImage(event)" type="file" name="new-image-input" id="new-image-input" accept="image/*">
        <label for="new-image-input"><ion-icon name="add"></ion-icon></label>
        <div style="display: none;">
          <button onclick="closePreview()" id="close-preview-image-btn" type="button"><ion-icon
              name="close"></ion-icon></button>
          <button onclick="acceptPreview()" id="accept-preview-image-btn" type="button"><ion-icon
              name="checkmark"></ion-icon></button>
        </div>
      </div>
    </div>

    <div id="edit-item-description-container">
      <h2>
        Descrição
      </h2>
      <textarea id="edit-item-description" name="item_description" rows="10"
        placeholder="Descrição do anúncio"></textarea>
      <ol id="edit-item-category-list">
        <li>
          <label for="category">Categoria</label>
          <select name="category" id="category">
            <?php foreach ($categories as $category): ?>
              <option value=<?= $category->id ?>><?= $category->name ?></option>
            <?php endforeach; ?>
          </select>
        </li>
      </ol>
    </div>

    <div id="edit-item-info">
      <p class="small-font-size">Publicado
        <?= date('d/m/Y'); ?>
      </p>
      <input type="text" name="item_name" id="edit-item-name" placeholder="Nome do anúncio">
      <span>
        <input type="text" name="item_price" id="edit-item-price" placeholder="Preço">
        €
      </span>
      <button id="add-to-cart-btn" class="deactivated-btn" type="button">Adicionar ao carrinho</button>
      <button id="negotiate-btn" class="deactivated-btn" type="button">Propor outro preço</button>
      <button id="send-message-btn" class="deactivated-btn" type="button">Enviar mensagem</button>
    </div>

    <div id="item-location" class="edit-item-deactivated">
      <h3>Localização</h3>
      <div>
        <div>
          <h4><?= $user->city ?></h4>
          <p><?= $user->state . ', ' . $user->country ?></p>
        </div>
        <img src="https://www.olx.pt/app/static/media/staticmap.65e20ad98.svg" alt="Location Map">
      </div>
    </div>

    <div id="seller-info">
      <h3>Utilizador</h3>
      <div>
        <img id="seller-img" src="<?= '/../' . $user->image ?>" alt="User Profile Picture">
        <div>
          <h4 id="seller-name"><?= $user->name() ?></h4>
          <p class="small-font-size">
            No eKo desde
            <?= $user->registration_date->format('d/m/Y'); ?>
          </p>
          <!-- <p class="small-font-size">Esteve online dia 07 de abril de 2024</p> -->
        </div>
      </div>
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
          <p class="small-font-size"><?= count($seller_reviews) ?> classificações</p>
        <?php endif; ?>
      </div>
    </div>

  </form>
<?php } ?>

<?php function drawItems(Session $session, $order)
{ ?>
  <section id="items">

    <header>
      <h2></h2>
      <div>
        <p>Ordenar por:</p>
        <select name="items-order" id="items-order">
          <option value="relevance:desc" <?= $order === 'relevance:desc' ? 'selected' : ''; ?>>Anúncios recomendados</option>
          <option value="price:asc" <?= $order === 'price:asc' ? 'selected' : ''; ?>>Mais barato</option>
          <option value="price:desc" <?= $order === 'price:desc' ? 'selected' : ''; ?>>Mais caro</option>
          <option value="created_at:desc" <?= $order === 'created_at:desc' ? 'selected' : ''; ?>>Mais recente</option>
          <option value="created_at:asc" <?= $order === 'created_at:asc' ? 'selected' : ''; ?>>Mais antigo</option>
        </select>
      </div>
    </header>
    <ol id="items-container">
    </ol>

    <nav>
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
  <?php $order = 'relevance:desc'; ?>
  <!-- This is currently static - TODO make dinamic:
    -> Get items depending on the user profile from db; -->

  <section id="items">

    <header>
      <h2></h2>
      <div>
        <p>Ordenar por:</p>
        <select name="items-order" id="items-order">
          <option value="relevance:desc" <?= $order === 'relevance:desc' ? 'selected' : ''; ?>>Anúncios recomendados</option>
          <option value="price:asc" <?= $order === 'price:asc' ? 'selected' : ''; ?>>Mais barato</option>
          <option value="price:desc" <?= $order === 'price:desc' ? 'selected' : ''; ?>>Mais caro</option>
          <option value="created_at:desc" <?= $order === 'created_at:desc' ? 'selected' : ''; ?>>Mais recente</option>
          <option value="created_at:asc" <?= $order === 'created_at:asc' ? 'selected' : ''; ?>>Mais antigo</option>
        </select>
      </div>
    </header>
    <ol id="items-container">
    </ol>

    <nav>
    </nav>

  </section>

  <!-- <section id="items">
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

  </section> -->
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

<?php function drawWishlistItems(Session $session, array $wishlist)
{ ?>
  <section id="items" class="wishlist">
    <h2>A minha whishlist:</h2>
    <?php if (empty($wishlist)): ?>
      <h2 id="empty-cart-title">O sua wishlist está vazia.</h2>
    <?php else: ?>
      <ol id="items-container">
        <?php
        foreach ($wishlist as $wishlistItem) {
          drawWishlistItem($session, $wishlistItem['item'], $wishlistItem['seller'], $wishlistItem['isItemInCart']);
        }
        ?>
      </ol>
    <?php endif; ?>
  </section>
<?php } ?>

<?php function drawWishlistItem(Session $session, Item $item, User $seller, bool $isItemInCart)
{ ?>
  <li data-item-id="<?= $item->id ?>" data-item-in-cart="<?= $isItemInCart ? 1 : 0 ?>">
    <div>
      <div>
        <h3><?= $item->name ?></h3>
        <div>
          <h3><?= $item->price ?> €</h3>
        </div>
      </div>
      <div>
        <div>
          <h4><?= $seller->city ?></h4>
          <p><?= $seller->state . ", " . $seller->country ?></p>
        </div>
        <div>
          <button class="wishlist-btn"><ion-icon name="heart"></ion-icon></button>
          <button class="cart-btn"><ion-icon name="cart<?= $isItemInCart ? "" : "-outline" ?>"></ion-icon></button>
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