<?php
declare(strict_types=1);

require_once (__DIR__ . '/../database/item.class.php');
require_once (__DIR__ . '/../utils/session.php');
?>

<?php function drawInbox(Session $session)
{ ?>
  <!-- This is currently static - TODO make dinamic: -->
  <section id="inbox">
    <ol id="inbox-container">
      <?php
      drawInboxChat();
      drawInboxChat();
      ?>
    </ol>
  </section>
<?php } ?>

<?php function drawInboxChat()
{ ?>
  <li>
    <div class="inbox-chat-item">

      <img src="https://ireland.apollo.olxcdn.com/v1/files/5inzf0kibmye2-PT/image;s=1000x700" alt="Item Image">
      <div>
        <div>
          <h3>Lexus GS 450H - Garantia - Nacional - Bastantes Extras - 345cv</h3>
          <button><ion-icon name="heart-outline"></ion-icon></button>
        </div>
        <div>
          <div>
            <h3>15.990 €</h3>
            <p>Negociável</p>
          </div>
        </div>
      </div>

    </div>

    <div class="inbox-chat-msg">
      <div>
        <h4>Luís Figo</h4>
        <span>há 3 min</span>
      </div>
      <div>
        <span>Eu: </span>
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Ipsa doloribus vel quis adipisci non vitae eius
          saepe.</p>
      </div>
    </div>

  </li>
<?php } ?>