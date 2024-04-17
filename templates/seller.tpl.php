<?php
declare(strict_types=1);

require_once (__DIR__ . '/../templates/search-bar.tpl.php');
?>

<?php function drawSellerDashboardAnalytics(Session $session)
{ ?>
  <section id="seller-dashboard-analytics-section">
    <div>
      <h3>Receita: </h3>
      <h2>23.210 €</h2>
    </div>
    <div>
      <h3>Promovidos: </h3>
      <h2>10</h2>
    </div>
    <div>
      <h3>Vendidos: </h3>
      <h2>53</h2>
    </div>
    <div>
      <h3>Por enviar: </h3>
      <h2>2</h2>
    </div>
    <div>
      <h3>Por vender: </h3>
      <h2>3</h2>
    </div>
  </section>
<?php } ?>