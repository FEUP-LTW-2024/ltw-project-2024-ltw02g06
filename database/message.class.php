<?php
declare(strict_types=1);

require_once (__DIR__ . '/../database/item.class.php');

class Message
{

  public int $id;
  public int $item_id;
  public string $item_name;
  public float $item_price;
  public int $item_seller;
  public int $sender;
  public string $sender_first_name;
  public string $sender_last_name;
  public int $receiver;
  public string $receiver_first_name;
  public string $receiver_last_name;
  public ?string $message;
  public DateTime $timestamp;
  public string $type;
  public ?float $value;
  public ?bool $accepted;

  public function __construct(
    int $id,
    int $item_id,
    string $item_name,
    float $item_price,
    int $item_seller,
    int $sender,
    string $sender_first_name,
    string $sender_last_name,
    int $receiver,
    string $receiver_first_name,
    string $receiver_last_name,
    ?string $message,
    DateTime $timestamp,
    string $type,
    ?float $value,
    ?bool $accepted,
  ) {
    $this->id = $id;
    $this->item_id = $item_id;
    $this->item_name = $item_name;
    $this->item_price = $item_price;
    $this->item_seller = $item_seller;
    $this->sender = $sender;
    $this->sender_first_name = $sender_first_name;
    $this->sender_last_name = $sender_last_name;
    $this->receiver = $receiver;
    $this->receiver_first_name = $receiver_first_name;
    $this->receiver_last_name = $receiver_last_name;
    $this->message = $message;
    $this->timestamp = $timestamp;
    $this->type = $type;
    $this->value = $value;
    $this->accepted = $accepted;
  }

  static function getInbox(PDO $db, int $user_id, ?string $search): array
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

    $whereConditions[':user_id'] = $user_id;

    if ($search) {
      $search_modified = str_replace([' ', ','], '%', $search);

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
      $whereConditions[':search'] = '%' . $search_modified . '%';
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

  static function getMessage(PDO $db, int $message_id): ?Message
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
    $stmt->execute([$message_id]);

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

  static function sendMessage(PDO $db, array $message_data): ?Message
  {
    $negotiation = isset($message_data['value']);

    $item = Item::getItem($db, $message_data['item_id']);

    if ($item->status != 'active')
      return null;

    if ($negotiation) {
      $stmt = $db->prepare('
          INSERT INTO message (item, sender, receiver, value, type, message, accepted) 
          VALUES (?, ?, ?, ?, ?, ?, ?)');
      $stmt->execute([
        $message_data['item_id'],
        $message_data['sender'],
        $message_data['receiver'],
        $message_data['value'],
        'negotiation',
        $message_data['message'],
        $item->seller == $message_data['sender'] ? true : null,
      ]);
    } else {
      $stmt = $db->prepare('
          INSERT INTO message (item, sender, receiver, message, accepted) 
          VALUES (?, ?, ?, ?, ?)');
      $stmt->execute([
        $message_data['item_id'],
        $message_data['sender'],
        $message_data['receiver'],
        $message_data['message'],
        null,
      ]);
    }

    $message_id = $db->lastInsertId();


    return Message::getMessage($db, (int) $message_id);
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

  static function updateMessage(PDO $db, int $message_id, bool $accepted): Message
  {
    try {
      $stmt = $db->prepare('
          UPDATE message
          SET accepted = ?
          WHERE id = ?
      ');
      $stmt->execute([$accepted, $message_id]);

      return Message::getMessage($db, $message_id);
    } catch (PDOException $e) {
      throw $e; // Re-throwing the exception to be caught in the calling code
    }
  }
}
?>