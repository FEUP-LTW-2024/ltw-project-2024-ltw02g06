<?php function drawSearchBar()
{ ?>
  <div id="search-bar-container">

    <div id="search-bar">
      <ion-icon name="search-outline"></ion-icon>
      <input type="text" placeholder="O que procuras?">
    </div>

    <div id="search-location">
      <ion-icon name="location-outline"></ion-icon>
      <input type="text" placeholder="Todo o país">
    </div>

    <button>Pesquisar</button>

  </div>
<?php } ?>

<?php function drawSmallSearchBar()
{ ?>
  <div id="small-search-bar-container">

    <div id="small-search-bar">
      <ion-icon name="search-outline"></ion-icon>
      <input type="text" placeholder="O que procuras?">
    </div>

    <button>Pesquisar</button>

  </div>
<?php } ?>