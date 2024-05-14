<?php function drawProfile(Session $session, User $user)
{ ?>
  <section id="profile">
    <?php drawProfileInfo($session, $user) ?>
  </section>

<?php } ?>

<?php function drawProfileInfo(Session $session, User $user)
{ ?>
  <?php
  $id = $session->getId();
  ?>
  <div id="profile-info">
    <img id="profile-img" src="/../<?= $user->image ?>" alt="User Profile Picture">
    <div>
      <div>
        <h2 id="profile-name"><?= $user->first_name . " " . $user->last_name ?></h2>
        <h3><?= $id == $user->id ? "$user->address, $user->zipcode -" : "" ?><?= $user->city ?></h3>
        <h5><?= $user->state . ", " . $user->country ?></h5>
      </div>
      <div>
        <p>No eKo desde
          <?= $user->registration_date->format('d/m/Y'); ?>
        </p>
      </div>
    </div>

    <?php if ($id == $user->id): ?>
      <div id="edit-profile-btn-container">
        <a href="profile.edit.php" title="Editar perfil">
          <ion-icon name="settings-outline"></ion-icon>
        </a>
      </div>
    <?php endif; ?>
  </div>
<?php } ?>

<?php function drawProfileReviews(Session $session)
{ ?>
  <!-- This is currently static - TODO make dinamic:
    -> Get user info from db; -->
  <div id="reviews">

    <h3>Avaliações:&nbsp;&nbsp;<b>4.3/5</b></h3>
    <p>17 classificações</p>
    <div>
      <button id="previous-review-btn"><ion-icon name="chevron-back"></ion-icon></button>
      <button id="next-review-btn"><ion-icon name="chevron-forward"></ion-icon></button>
    </div>
    <ul>

      <li>
        <div>
          <h4>Luís Figo</h4>
          <h5>4/5</h5>
        </div>
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Vero ipsa excepturi alias quos voluptas sunt
          reprehenderit rerum beatae omnis esse, corporis labore voluptate accusamus nostrum eaque debitis nisi aperiam
          iste.</p>
      </li>

    </ul>

  </div>
<?php } ?>

<?php function drawEditProfile(Session $session, User $user)
{ ?>
  <form id="edit-profile"
    action="../actions/action_edit_profile.php<?= isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : '?redirect=' . urlencode('/pages/profile.php') ?>"
    method="post">

    <input type="hidden" name="user_id" value=<?= htmlspecialchars($user->id) ?>>

    <div id="edit-profile-buttons">
      <button id="edit-profile-cancel-btn" type="button">Cancelar<ion-icon name="close"></ion-icon></button>
      <button id="edit-profile-submit-btn" type="submit">Confirmar<ion-icon name="checkmark" submit></ion-icon></button>
    </div>

    <div id="edit-profile-image-container">
      <div>
        <img id="profile-image-preview" src="/<?= $user->image ?>" alt="Item Image">
        <label for="new-image-input"><ion-icon name="add"></ion-icon></label>
        <input type="hidden" name="new_image_path" id="new-image-path">
        <input type="file" name="new-image" id="new-image-input" accept="image/*">
      </div>
    </div>

    <div id="edit-profile-name">
      <h3>Informações Pessoais</h3>
      <div>
        <label for="first_name">Primeiro nome:</label>
        <input type="text" name="first_name" placeholder="Primeiro nome" value=<?= htmlspecialchars($user->first_name) ?>>
        <label for="last_name">Sobrenome:</label>
        <input type="text" name="last_name" placeholder="Sobrenome" value=<?= htmlspecialchars($user->last_name) ?>>
        <label for="email">Email:</label>
        <input type="text" name="email" placeholder="Email" value=<?= htmlspecialchars($user->email) ?>>
      </div>
    </div>
    <div id="edit-profile-location">
      <h3>Localização</h3>
      <div>
        <label for="address">Morada:</label>
        <input type="text" name="address" placeholder="Morada" value="<?= htmlspecialchars($user->address) ?>">
        <label for="zipcode">Código postal:</label>
        <input type="text" name="zipcode" placeholder="Código postal" value="<?= htmlspecialchars($user->zipcode) ?>">
        <label for="city">Cidade:</label>
        <input type="text" name="city" placeholder="Cidade" value="<?= htmlspecialchars($user->city) ?>">
        <label for="state">Distrito:</label>
        <input type="text" name="state" placeholder="Distrito" value="<?= htmlspecialchars($user->state) ?>">
        <label for="country">País:</label>
        <input type="text" name="country" placeholder="País" value="<?= htmlspecialchars($user->country) ?>">
      </div>

    </div>
  </form>

<?php } ?>