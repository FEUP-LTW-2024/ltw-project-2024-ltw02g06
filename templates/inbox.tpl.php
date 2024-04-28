<?php
declare(strict_types=1);

require_once (__DIR__ . '/../database/item.class.php');
require_once (__DIR__ . '/../utils/session.php');
?>

<?php function drawInbox(Session $session, array $inbox)
{ ?>

  <?php
  $id = $session->getId();
  ?>

  <section id="inbox">
    <ol id="inbox-container">
      <?php
      foreach ($inbox as $item_id => $chat) {
        drawInboxChat($id, $chat);
      }
      ?>
    </ol>
  </section>
<?php } ?>

<?php function drawInboxChat(int $user_id, array $chat)
{ ?>
  <?php
  $timestamp = $chat[0]->timestamp;
  $currentTime = new DateTime();
  $timeDifference = $currentTime->diff($timestamp);

  $years = $timeDifference->y;
  $months = $timeDifference->m;
  $days = $timeDifference->d;
  $hours = $timeDifference->h;
  $minutes = $timeDifference->i;
  $seconds = $timeDifference->s;

  $timeAgo = "há ";
  if ($years > 0) {
    $timeAgo .= $years . ($years == 1 ? ' ano' : ' anos');
  } elseif ($months > 0) {
    $timeAgo .= $months . 'm';
  } elseif ($days > 0) {
    $timeAgo .= $days . 'd';
  } elseif ($hours > 0) {
    $timeAgo .= $hours . 'h';
  } elseif ($minutes > 0) {
    $timeAgo .= $minutes . 'min';
  } elseif ($seconds > 0) {
    $timeAgo .= $seconds . 's';
  } else {
    $timeAgo = "agora";
  }

  ?>
  <li>
    <div class="inbox-chat-item">

      <!-- <img src="https://ireland.apollo.olxcdn.com/v1/files/5inzf0kibmye2-PT/image;s=1000x700" alt="Item Image"> -->
      <!-- <button><ion-icon name="heart-outline"></ion-icon></button> -->
      <h3><?= $chat[0]->item_name ?></h3>
      <div>
        <h3><?= $chat[0]->item_price ?> €</h3>
        <!-- <p>Negociável</p> -->
      </div>

    </div>

    <div class="inbox-chat-msg">
      <div>
        <!-- TODO Correct this: the sender name and receiver name might be needed -->
        <h4>
          <?= $chat[0]->receiver == $user_id ? $chat[0]->sender_first_name . " " . $chat[0]->sender_last_name : $chat[0]->receiver_first_name . " " . $chat[0]->receiver_last_name ?>
        </h4>
        <span><?= $timeAgo ?></span>
      </div>
      <div>
        <span><?= $chat[0]->sender == $user_id ? "Eu:" : $chat[0]->sender_first_name . ":" ?>
        </span>
        <p><?= $chat[0]->message ?></p>
      </div>
    </div>

  </li>
<?php } ?>