<?php
declare(strict_types=1);

require_once (__DIR__ . '/../templates/search-bar.tpl.php');
require_once (__DIR__ . '/../templates/item.tpl.php');
?>

<?php function drawAdminCategoriesSection()
{ ?>
  <!-- This is currently static - TODO make dinamic: -->
  <section id="admin-categories-section">
    <h2>Categorias:</h2>
    <ul>
      <li>
        <p>Carros</p>
        <button title="Editar"><ion-icon name="create-outline"></ion-icon></button>
        <button title="Remover"><ion-icon name="trash-outline"></ion-icon></button>
      </li>
      <li>
        <p>Motas</p>
        <button title="Editar"><ion-icon name="create-outline"></ion-icon></button>
        <button title="Remover"><ion-icon name="trash-outline"></ion-icon></button>
      </li>
      <li>
        <p>Tecnologia</p>
        <button title="Editar"><ion-icon name="create-outline"></ion-icon></button>
        <button title="Remover"><ion-icon name="trash-outline"></ion-icon></button>
      </li>
      <li>
        <p>Roupa</p>
        <button title="Editar"><ion-icon name="create-outline"></ion-icon></button>
        <button title="Remover"><ion-icon name="trash-outline"></ion-icon></button>
      </li>
    </ul>
  </section>
<?php } ?>

<?php function drawAdminReportedItemsSection()
{ ?>
  <!-- This is currently static - TODO make dinamic: -->
  <section id="admin-reported-items-section">
    <h2>Anúncios reportados:</h2>
    <?php
    drawAdminReportedItems();
    ?>
  </section>
<?php } ?>

<?php function drawAdminUsersSection()
{ ?>
  <!-- This is currently static - TODO make dinamic: -->
  <section id="admin-users-section">
    <?php
    drawSmallSearchBar();
    ?>

    <div>
      <div>
        <h2>Adminstradores:</h2>
        <ul id="admins-list">
          <li>
            <div>
              <h3>Luís Figo</h3>
              <p>#10</p>
            </div>
            <div>
              <button title="Remover cargo"><ion-icon name="star-half-outline"></ion-icon></button>
              <button title="Apagar utilizador"><ion-icon name="trash-outline"></ion-icon></button>
            </div>
          </li>
          <li>
            <div>
              <h3>Luís Figo</h3>
              <p>#10</p>
            </div>
            <div>
              <button title="Remover cargo"><ion-icon name="star-half-outline"></ion-icon></button>
              <button title="Apagar utilizador"><ion-icon name="trash-outline"></ion-icon></button>
            </div>
          </li>
          <li>
            <div>
              <h3>Luís Figo</h3>
              <p>#10</p>
            </div>
            <div>
              <button title="Remover cargo"><ion-icon name="star-half-outline"></ion-icon></button>
              <button title="Apagar utilizador"><ion-icon name="trash-outline"></ion-icon></button>
            </div>
          </li>
          <li>
            <div>
              <h3>Luís Figo</h3>
              <p>#10</p>
            </div>
            <div>
              <button title="Remover cargo"><ion-icon name="star-half-outline"></ion-icon></button>
              <button title="Apagar utilizador"><ion-icon name="trash-outline"></ion-icon></button>
            </div>
          </li>
        </ul>
      </div>
      <div>
        <h2>Utilizadores:</h2>
        <ul id="users-list">
          <li>
            <div>
              <h3>Luís Figo</h3>
              <p>#10</p>
            </div>
            <div>
              <button title="Tornar administrador"><ion-icon name="star-outline"></ion-icon></button>
              <button title="Apagar utilizador"><ion-icon name="trash-outline"></ion-icon></button>
            </div>
          </li>
          <li>
            <div>
              <h3>Luís Figo</h3>
              <p>#10</p>
            </div>
            <div>
              <button title="Tornar administrador"><ion-icon name="star-outline"></ion-icon></button>
              <button title="Apagar utilizador"><ion-icon name="trash-outline"></ion-icon></button>
            </div>
          </li>
          <li>
            <div>
              <h3>Luís Figo</h3>
              <p>#10</p>
            </div>
            <div>
              <button title="Tornar administrador"><ion-icon name="star-outline"></ion-icon></button>
              <button title="Apagar utilizador"><ion-icon name="trash-outline"></ion-icon></button>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </section>
<?php } ?>

<?php function drawAdminChangesHistorySection()
{ ?>
  <!-- This is currently static - TODO make dinamic: -->
  <section id="admin-changes-history-section">
    <div>
      <h2>Registro de auditoria:</h2>
      <button title="Donwload registro de auditoria"><ion-icon name="download-outline"></ion-icon></button>
    </div>
    <ul>
      <li>
        <p>Date</p>
        <p>User_id</p>
        <p>User_name</p>
        <p>Ação</p>
      </li>
      <li>
        <p>Date</p>
        <p>User_id</p>
        <p>User_name</p>
        <p>Ação</p>
      </li>
      <li>
        <p>Date</p>
        <p>User_id</p>
        <p>User_name</p>
        <p>Ação</p>
      </li>
      <li>
        <p>Date</p>
        <p>User_id</p>
        <p>User_name</p>
        <p>Ação</p>
      </li>
      <li>
        <p>Date</p>
        <p>User_id</p>
        <p>User_name</p>
        <p>Ação</p>
      </li>
    </ul>
  </section>
<?php } ?>