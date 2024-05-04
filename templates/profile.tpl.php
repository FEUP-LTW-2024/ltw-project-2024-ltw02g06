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
        <h3><?= $user->city ?></h3>
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