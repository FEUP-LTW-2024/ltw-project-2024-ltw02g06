<?php
declare(strict_types=1);

require_once (__DIR__ . '/../templates/search-bar.tpl.php');
require_once (__DIR__ . '/../templates/item.tpl.php');
?>

<?php function drawAdminCategoriesSection()
{ ?>
  <section id="admin-categories-section">
    <h2>Categorias:</h2>
    <ul>
    </ul>
    <div>
      <input id="new-category-input" placeholder="Categoria" type="text">
      <button id="add-category-button" type="button"><ion-icon name="add"></ion-icon></button>
    </div>
  </section>
<?php } ?>

<?php function drawAdminUsersSection()
{ ?>
  <section id="admin-users-section">
    <?php
    drawSmallSearchBar();
    ?>
    <div>
      <div>
        <h2>Adminstradores:</h2>
        <ul id="admins-list">
        </ul>
      </div>
      <div>
        <h2>Utilizadores:</h2>
        <ul id="users-list">
        </ul>
      </div>
    </div>
  </section>
<?php } ?>