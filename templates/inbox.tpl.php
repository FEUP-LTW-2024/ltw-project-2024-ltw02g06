<?php
declare(strict_types=1);

require_once (__DIR__ . '/../database/item.class.php');
require_once (__DIR__ . '/../database/user.class.php');
require_once (__DIR__ . '/../database/message.class.php');
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

<?php function getTimeAgo(DateTime $timestamp)
{
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

  return $timeAgo;
} ?>

<?php function drawInboxChat(int $user_id, array $chat)
{ ?>
  <?php
  $timestamp = $chat[0]->timestamp;
  $timeAgo = getTimeAgo($timestamp);

  ?>
  <li>
    <div class="inbox-chat-item">

      <h3><?= htmlspecialchars(trim($chat[0]->item_name)) ?></h3>
      <div>
        <h3><?= $chat[0]->item_price ?> €</h3>
      </div>

    </div>

    <div class="inbox-chat-msg">
      <div>
        <h4>
          <?= $chat[0]->receiver == $user_id ? htmlspecialchars($chat[0]->sender_first_name . " " . $chat[0]->sender_last_name) : htmlspecialchars($chat[0]->receiver_first_name . " " . $chat[0]->receiver_last_name) ?>
        </h4>
        <span><?= $timeAgo ?></span>
      </div>
      <div>
        <span><?= $chat[0]->sender == $user_id ? "Eu:" : htmlspecialchars($chat[0]->sender_first_name . ":") ?>
        </span>
        <p><?= htmlspecialchars(trim($chat[0]->message)) ?></p>
      </div>
    </div>

  </li>
<?php } ?>


<?php function drawChatHeader(int $user_id, Item $item, User $seller, array $chat)
{ ?>

  <header id="chat-header">
    <div class="chat-item">

      <h3><?= htmlspecialchars(trim($item->name)) ?></h3>
      <div>
        <h3><?= $item->price ?> €</h3>
      </div>

    </div>

    <div class="chat-other-user">
      <div>
        <h4>
          <?php if (empty($chat)): ?>
            <?= htmlspecialchars($seller->first_name . " " . $seller->last_name) ?>
          <?php else: ?>
            <?= $chat[0]->receiver == $user_id ? htmlspecialchars($chat[0]->sender_first_name . " " . $chat[0]->sender_last_name) : htmlspecialchars($chat[0]->receiver_first_name . " " . $chat[0]->receiver_last_name); ?>
          <?php endif; ?>
        </h4>
      </div>
    </div>

  </header>
<?php } ?>

<?php function drawChat(Session $session, array $chat, int $other_user, Item $item, User $seller)
{ ?>

  <?php
  $id = $session->getId();
  ?>

  <section id="chat">
    <?php drawChatHeader($id, $item, $seller, $chat); ?>
    <?php if (empty($chat)): ?>
      <h2 id="start-chat">Inicie a coversa com <?= htmlspecialchars(trim($seller->first_name)) ?>.</h2>
    <?php endif; ?>
    <ol id="chat-messages">
    </ol>

    <form action="../actions/action_send_message.php" method="post">
      <input type="text" name="value" placeholder="Propor novo preço">
      <input type="hidden" name="item_id" value=<?= $item->id ?>>
      <input type="hidden" name="receiver" value=<?= $other_user ?>>
      <input type="text" name="message" placeholder="Mensagem">
      <button>Enviar <ion-icon name="send"></ion-icon></button>
    </form>
  </section>
<?php } ?>

<?php function drawChatMessage(int $user_id, Message $message)
{ ?>
  <?php
  $timeAgo = getTimeAgo($message->timestamp);
  ?>

  <?php if ($message->type == 'negotiation'): ?>
    <li class=<?= $message->receiver == $user_id ? "received-proposition" : "sent-proposition" ?>
      data-message-id=<?= $message->id ?>>
      <div class="proposition-value">
        <h3>Proposta: <?= $message->value ?> €</h3>
        <span><?= $timeAgo ?></span>
      </div>
      <p>
        <?= htmlspecialchars(trim($message->message)) ?>
      </p>
      <div class="proposition-btns">
        <?php if ($message->sender == $user_id): ?>

          <?php if ($message->accepted === null): ?>
            <h5>Por decidir</h5>
          <?php elseif ($message->accepted): ?>
            <?php if ($message->item_seller === $user_id): ?>
              <h5>Aceite</h5>
            <?php else: ?>
              <button class="add-to-cart-proposition-btn">Adicionar ao carrinho<ion-icon name="cart-outline"></ion-icon></button>
            <?php endif; ?>
          <?php else: ?>
            <h5>Rejeitado</h5>
          <?php endif; ?>

        <?php elseif ($message->receiver == $user_id): ?>

          <?php if ($message->accepted === null): ?>
            <button class="reject-proposition-btn">Rejeitar<ion-icon name="close"></ion-icon></button>
            <button class="accept-proposition-btn">Aceitar<ion-icon name="checkmark"></ion-icon></button>
          <?php elseif ($message->accepted): ?>
            <h5>Aceite</h5>
          <?php else: ?>
            <h5>Rejeitado</h5>
          <?php endif; ?>

        <?php endif; ?>
      </div>
    </li>
  <?php else: ?>
    <li class=<?= $message->receiver == $user_id ? "received-message" : "sent-message" ?>>
      <div>
        <p>
          <?= htmlspecialchars(trim($message->message)) ?>
          <span><?= $timeAgo ?></span>
        </p>
      </div>
    </li>
  <?php endif; ?>


<?php } ?>