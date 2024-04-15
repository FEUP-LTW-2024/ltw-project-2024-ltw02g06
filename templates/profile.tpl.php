<?php function drawProfile(Session $session)
{ ?>
  <section id="profile">
    <?php drawProfileInfo($session) ?>
    <?php drawProfileReviews($session) ?>
  </section>

<?php } ?>

<?php function drawProfileInfo(Session $session)
{ ?>
  <!-- This is currently static - TODO make dinamic:
    -> Get user info from db; -->
  <div id="profile-info">
    <img id="profile-img"
      src="https://publish-p47754-e237306.adobeaemcloud.com/adobe/dynamicmedia/deliver/dm-aid--3a108752-74c3-4677-aefc-32338d719b1c/_330186262719.app.png?preferwebp=true&width=312"
      alt="User Profile Picture">
    <div>
      <div>
        <h2 id="profile-name">Luís Figo</h2>
        <h3>Custóias, Leça Do Balio E Guifões,</h3>
        <h3>Porto</h3>
      </div>
      <div>
        <p>No eKo desde abril de 2015</p>
        <p>Esteve online dia 07 de abril de 2024</p>
      </div>
    </div>
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