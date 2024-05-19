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
        <h2 id="profile-name" title="<?= htmlspecialchars($user->firstName . " " . $user->lastName) ?>">
          <?= htmlspecialchars($user->firstName . " " . $user->lastName) ?>
        </h2>
        <h3
          title="<?= $id == $user->id ? htmlspecialchars("$user->address, $user->zipcode - ") : "" ?><?= htmlspecialchars($user->city) ?>">
          <?= $id == $user->id ? htmlspecialchars("$user->address, $user->zipcode -") : "" ?>
          <?= htmlspecialchars($user->city) ?>
        </h3>
        <h5 title="<?= htmlspecialchars($user->state . ", " . $user->country) ?>">
          <?= htmlspecialchars($user->state . ", " . $user->country) ?>
        </h5>
      </div>
      <div>
        <p>No eKo desde
          <?= $user->registrationDate->format('d/m/Y'); ?>
        </p>
      </div>
    </div>

    <?php if ($id == $user->id): ?>
      <div id="edit-profile-btn-container">
        <a href="profile.edit.php" title="Editar perfil">
          <ion-icon name="settings-outline"></ion-icon>
        </a>
        <a href="password.edit.php" title="Mudar password">
          <ion-icon name="lock-closed-outline"></ion-icon>
        </a>
        <a href="boughtItems.php" title="Histórico de compras">
          <ion-icon name="reader-outline"></ion-icon>
        </a>
      </div>
    <?php endif; ?>
  </div>
<?php } ?>

<?php function drawEditProfile(Session $session, User $user)
{ ?>
  <form id="edit-profile"
    action="../actions/action_edit_profile.php<?= isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : '?redirect=' . urlencode('/pages/profile.php') ?>"
    method="post">

    <input type="hidden" name="userId" value=<?= $user->id ?>>
    <input type="hidden" name="csrf" value="<?= $session->getSessionToken() ?>">

    <div id="edit-profile-buttons">
      <button id="edit-profile-cancel-btn" type="button">Cancelar<ion-icon name="close"></ion-icon></button>
      <button id="edit-profile-submit-btn" type="submit">Confirmar<ion-icon name="checkmark" submit></ion-icon></button>
    </div>

    <div id="edit-profile-image-container">
      <div>
        <img id="profile-image-preview" src="/<?= $user->image ?>" alt="Item Image">
        <label for="new-image-input"><ion-icon name="add"></ion-icon></label>
        <input type="hidden" name="newImagePath" id="new-image-path">
        <input type="file" name="new-image" id="new-image-input" accept="image/*">
      </div>
    </div>

    <div id="edit-profile-name">
      <h3>Informações Pessoais</h3>
      <div>
        <label for="firstName">Primeiro nome:</label>
        <input type="text" name="firstName" placeholder="Primeiro nome" value=<?= htmlspecialchars(trim($user->firstName)) ?>>
        <label for="lastName">Sobrenome:</label>
        <input type="text" name="lastName" placeholder="Sobrenome" value=<?= htmlspecialchars(trim($user->lastName)) ?>>
        <label for="email">Email:</label>
        <input type="text" name="email" placeholder="Email" value=<?= htmlspecialchars(trim($user->email)) ?>>
      </div>
    </div>
    <div id="edit-profile-location">
      <h3>Localização</h3>
      <div>
        <label for="address">Morada:</label>
        <input type="text" name="address" placeholder="Morada" value="<?= htmlspecialchars(trim($user->address)) ?>">
        <label for="zipcode">Código postal:</label>
        <input type="text" name="zipcode" placeholder="Código postal"
          value="<?= htmlspecialchars(trim($user->zipcode)) ?>">
        <label for="city">Cidade:</label>
        <input type="text" name="city" placeholder="Cidade" value="<?= htmlspecialchars(trim($user->city)) ?>">
        <label for="state">Distrito:</label>
        <input type="text" name="state" placeholder="Distrito" value="<?= htmlspecialchars(trim($user->state)) ?>">
        <label for="country">País:</label>
        <input type="text" name="country" placeholder="País" value="<?= htmlspecialchars(trim($user->country)) ?>">
      </div>

    </div>
  </form>

<?php } ?>

<?php function drawEditPassword(Session $session, User $user)
{ ?>
  <form id="edit-password" method="post">

    <input type="hidden" name="userId" value=<?= $user->id ?>>
    <input type="hidden" name="csrf" value="<?= $session->getSessionToken() ?>">

    <div id="edit-password-buttons">
      <button id="edit-password-cancel-btn" type="button">Cancelar<ion-icon name="close"></ion-icon></button>
      <button id="edit-password-submit-btn" type="submit">Confirmar<ion-icon name="checkmark" submit></ion-icon></button>
    </div>

    <div id="edit-password-container">
      <label for="password">Password atual:</label>
      <input type="password" name="password" placeholder="Password atual">
      <label for="newPassword">Password nova:</label>
      <input type="password" name="newPassword" placeholder="Password nova">
      <label for="confirmNewPassword">Confirmar password:</label>
      <input type="password" name="confirmNewPassword" placeholder="Password nova">
    </div>

  </form>

<?php } ?>