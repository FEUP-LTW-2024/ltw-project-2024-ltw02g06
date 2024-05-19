<?php
declare(strict_types=1);

require_once (__DIR__ . '/../database/item.class.php');

class Message
{

  public int $id;
  public int $itemId;
  public string $itemName;
  public float $itemPrice;
  public int $itemSeller;
  public int $sender;
  public string $senderFirstName;
  public string $senderLastName;
  public int $receiver;
  public string $receiverFirstName;
  public string $receiverLastName;
  public ?string $message;
  public DateTime $timestamp;
  public string $type;
  public ?float $value;
  public ?bool $accepted;

  public function __construct(
    int $id,
    int $itemId,
    string $itemName,
    float $itemPrice,
    int $itemSeller,
    int $sender,
    string $senderFirstName,
    string $senderLastName,
    int $receiver,
    string $receiverFirstName,
    string $receiverLastName,
    ?string $message,
    DateTime $timestamp,
    string $type,
    ?float $value,
    ?bool $accepted,
  ) {
    $this->id = $id;
    $this->itemId = $itemId;
    $this->itemName = $itemName;
    $this->itemPrice = $itemPrice;
    $this->itemSeller = $itemSeller;
    $this->sender = $sender;
    $this->senderFirstName = $senderFirstName;
    $this->senderLastName = $senderLastName;
    $this->receiver = $receiver;
    $this->receiverFirstName = $receiverFirstName;
    $this->receiverLastName = $receiverLastName;
    $this->message = $message;
    $this->timestamp = $timestamp;
    $this->type = $type;
    $this->value = $value;
    $this->accepted = $accepted;
  }

  static function getInbox(PDO $db, int $userId, ?string $search): array
  {
    $whereConditions = [];

    $query = 'SELECT message.id, message.item, item.name as item_name, item.price, item.seller, 
          sender_user.first_name as sender_first_name, sender_user.last_name as sender_last_name,
          receiver_user.first_name as receiver_first_name, receiver_user.last_name as receiver_last_name,
          message.sender, message.receiver, message.message, message.timestamp, 
          message.type, message.value, message.accepted
          FROM message
          INNER JOIN item ON message.item = item.id
          LEFT JOIN user as sender_user ON message.sender = sender_user.id
          LEFT JOIN user as receiver_user ON message.receiver = receiver_user.id
          INNER JOIN user as seller_user ON item.seller = seller_user.id
          WHERE (message.sender = :user_id OR message.receiver = :user_id) ';

    $whereConditions[':user_id'] = $userId;

    if ($search) {
      $searchModified = str_replace([' ', ','], '%', $search);

      $query .= ' AND (item.name LIKE :search
                OR item.description LIKE :search
                OR item.price LIKE :search
                OR seller_user.city LIKE :search
                OR seller_user.state LIKE :search
                OR seller_user.country LIKE :search
                OR (seller_user.city || " " || seller_user.state || " " || seller_user.country) LIKE :search
                OR seller_user.first_name LIKE :search
                OR seller_user.last_name LIKE :search
                OR (seller_user.first_name || " " || seller_user.last_name) LIKE :search)';
      $whereConditions[':search'] = '%' . $searchModified . '%';
    }

    $query .= ' AND item.status = "active"
              ORDER BY message.timestamp DESC';

    $stmt = $db->prepare($query);
    $stmt->execute($whereConditions);

    $messages = [];

    while ($row = $stmt->fetch()) {
      if (!isset($messages[$row['item']])) {
        $messages[$row['item']] = [];
      }

      $messages[$row['item']][] = new Message(
        $row['id'],
        $row['item'],
        $row['item_name'],
        $row['price'],
        $row['seller'],
        $row['sender'],
        $row['sender_first_name'],
        $row['sender_last_name'],
        $row['receiver'],
        $row['receiver_first_name'],
        $row['receiver_last_name'],
        $row['message'],
        new DateTime($row['timestamp']),
        $row['type'],
        $row['value'],
        $row['accepted'] === null ? null : (bool) $row['accepted']
      );
    }

    $messages = array_values($messages);

    return $messages;
  }

  static function getMessage(PDO $db, int $messageId): ?Message
  {

    $query = 'SELECT message.id, message.item, item.name as item_name, item.price, item.seller, 
          sender_user.first_name as sender_first_name, sender_user.last_name as sender_last_name,
          receiver_user.first_name as receiver_first_name, receiver_user.last_name as receiver_last_name,
          message.sender, message.receiver, message.message, message.timestamp, 
          message.type, message.value, message.accepted
          FROM message
          INNER JOIN item ON message.item = item.id
          LEFT JOIN user as sender_user ON message.sender = sender_user.id
          LEFT JOIN user as receiver_user ON message.receiver = receiver_user.id
          INNER JOIN user as seller_user ON item.seller = seller_user.id
          WHERE message.id = ? AND item.status = "active"';

    $stmt = $db->prepare($query);
    $stmt->execute([$messageId]);

    $message = $stmt->fetch();

    if (!$message)
      return null;

    return new Message(
      $message['id'],
      $message['item'],
      $message['item_name'],
      $message['price'],
      $message['seller'],
      $message['sender'],
      $message['sender_first_name'],
      $message['sender_last_name'],
      $message['receiver'],
      $message['receiver_first_name'],
      $message['receiver_last_name'],
      $message['message'],
      new DateTime($message['timestamp']),
      $message['type'],
      $message['value'],
      $message['accepted'] === null ? null : (bool) $message['accepted']
    );
  }

  static function sendMessage(PDO $db, array $messageData): ?Message
  {
    $negotiation = isset($messageData['value']);

    $item = Item::getItem($db, $messageData['itemId']);

    if ($item->status != 'active')
      return null;

    if ($negotiation) {
      $stmt = $db->prepare('
          INSERT INTO message (item, sender, receiver, value, type, message, accepted) 
          VALUES (?, ?, ?, ?, ?, ?, ?)');
      $stmt->execute([
        $messageData['itemId'],
        $messageData['sender'],
        $messageData['receiver'],
        $messageData['value'],
        'negotiation',
        $messageData['message'],
        $item->seller == $messageData['sender'] ? true : null,
      ]);
    } else {
      $stmt = $db->prepare('
          INSERT INTO message (item, sender, receiver, message, accepted) 
          VALUES (?, ?, ?, ?, ?)');
      $stmt->execute([
        $messageData['itemId'],
        $messageData['sender'],
        $messageData['receiver'],
        $messageData['message'],
        null,
      ]);
    }

    $messageId = $db->lastInsertId();


    return Message::getMessage($db, (int) $messageId);
  }

  static function getChat(PDO $db, int $item, int $sender, int $receiver): array
  {
    $query = 'SELECT message.id, message.item, item.name as item_name, item.price, item.seller, 
          sender_user.first_name as sender_first_name, sender_user.last_name as sender_last_name,
          receiver_user.first_name as receiver_first_name, receiver_user.last_name as receiver_last_name,
          message.sender, message.receiver, message.message, message.timestamp, 
          message.type, message.value, message.accepted
          FROM message
          INNER JOIN item ON message.item = item.id
          LEFT JOIN user as sender_user ON message.sender = sender_user.id
          LEFT JOIN user as receiver_user ON message.receiver = receiver_user.id
          INNER JOIN user as seller_user ON item.seller = seller_user.id
          WHERE ((message.sender = :sender AND message.receiver = :receiver) OR 
          (message.receiver = :sender AND message.sender = :receiver))
          AND message.item = :item ';

    $query .= ' AND item.status = "active"
              ORDER BY message.timestamp DESC';

    $stmt = $db->prepare($query);
    $whereConditions = [
      ':sender' => $sender,
      ':receiver' => $receiver,
      ':item' => $item,
    ];
    $stmt->execute($whereConditions);

    $messages = [];

    while ($row = $stmt->fetch()) {
      $messages[] = new Message(
        $row['id'],
        $row['item'],
        $row['item_name'],
        $row['price'],
        $row['seller'],
        $row['sender'],
        $row['sender_first_name'],
        $row['sender_last_name'],
        $row['receiver'],
        $row['receiver_first_name'],
        $row['receiver_last_name'],
        $row['message'],
        new DateTime($row['timestamp']),
        $row['type'],
        $row['value'],
        $row['accepted'] === null ? null : (bool) $row['accepted'],
      );
    }
    return $messages;
  }

  static function updateMessage(PDO $db, int $messageId, bool $accepted): Message
  {
    try {
      $stmt = $db->prepare('
          UPDATE message
          SET accepted = ?
          WHERE id = ?
      ');
      $stmt->execute([$accepted, $messageId]);

      return Message::getMessage($db, $messageId);
    } catch (PDOException $e) {
      throw $e; // Re-throwing the exception to be caught in the calling code
    }
  }
}
?>