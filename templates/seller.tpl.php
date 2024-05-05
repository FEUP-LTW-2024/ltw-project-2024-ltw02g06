<?php
declare(strict_types=1);

require_once (__DIR__ . '/../templates/search-bar.tpl.php');
?>

<?php function drawSellerDashboardAnalytics(Session $session, float $revenue, int $sold, int $toSend, int $active)
{ ?>
  <section id="seller-dashboard-analytics-section">
    <div>
      <h3>Receita: </h3>
      <h2 id="revenue"><?= $revenue ?> €</h2>
    </div>
    <!--
    <div>
      <h3>Promovidos: </h3>
      <h2>10</h2>
    </div>
    -->
    <div>
      <h3>Vendidos: </h3>
      <h2 id="sold"><?= $sold ?></h2>
    </div>
    <div>
      <h3>Por enviar: </h3>
      <h2 id="to-sent"><?= $toSend ?></h2>
    </div>
    <div>
      <h3>Por vender: </h3>
      <h2 id="active"><?= $active ?></h2>
    </div>
  </section>
<?php } ?>