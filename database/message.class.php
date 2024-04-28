<?php
declare(strict_types=1);

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
  public string $message;
  public DateTime $timestamp;
  public string $type;
  public ?float $value;
  public bool $accepted;

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
    string $message,
    DateTime $timestamp,
    string $type,
    ?float $value,
    bool $accepted,
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
      $query .= ' AND (item.name LIKE :search
                OR item.description LIKE :search
                OR item.price LIKE :search
                OR seller_user.city LIKE :search
                OR seller_user.state LIKE :search
                OR seller_user.country LIKE :search
                OR seller_user.first_name LIKE :search
                OR seller_user.last_name LIKE :search)';
      $whereConditions[':search'] = '%' . $search . '%';
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
        (bool) $row['accepted'],
      );
    }

    $messages = array_values($messages);

    return $messages;
  }
}
?>