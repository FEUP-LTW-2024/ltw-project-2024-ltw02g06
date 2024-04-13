<?php function drawBreadcrumbNav()
{ ?>
  <nav id="breadcrumb-nav">

    <button>
      <span><ion-icon name="chevron-back"></ion-icon></span>
      <span>Voltar</span>
    </button>

    <!-- This is currently static - TODO make dinamic:
    MAYBE save in the session the breadcrumbs in an array -->
    <ol>
      <li><a href="">Página principal</a></li>
      <li><a href="">Carros, motas e barcos</a></li>
      <li><a href="">Carros</a></li>
      <li><a href="">Lexus</a></li>
      <li><a href="">Lexus - Porto</a></li>
    </ol>

  </nav>
<?php } ?>