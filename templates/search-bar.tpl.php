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