<?php
declare(strict_types=1);

class Item
{
  public int $id;
  public string $name;
  public string $description;
  public float $price;
  public int $seller;
  public int $category;
  public string $status;
  public ?float $sold_price;
  public DateTime $creation_date;
  public int $clicks;
  public array $attributes = [];
  public array $images = [];

  public function __construct(
    int $id,
    string $name,
    string $description,
    float $price,
    int $seller,
    int $category,
    string $status,
    ?float $sold_price,
    DateTime $creation_date,
    int $clicks,
    array $attributes,
    array $images
  ) {
    $this->id = $id;
    $this->name = $name;
    $this->description = $description;
    $this->price = $price;
    $this->seller = $seller;
    $this->category = $category;
    $this->status = $status;
    $this->sold_price = $sold_price;
    $this->creation_date = $creation_date;
    $this->clicks = $clicks;
    $this->attributes = $attributes;
    $this->images = $images;
  }

  static function getItem(PDO $db, int $id): ?Item
  {
    $stmt = $db->prepare('
        SELECT id, name, description, price, seller, category, status, sold_price, creation_date, clicks
        FROM item
        WHERE id = ?
      ');
    $stmt->execute(array($id));

    $item = $stmt->fetch();

    if (!$item)
      return null;

    $stmt = $db->prepare('
        SELECT attribute as id, attribute.name, value
        FROM item_attributes
        LEFT JOIN attribute ON item_attributes.attribute = attribute.id
        WHERE item = ?
      ');
    $stmt->execute(array($id));

    $attributes = [];

    while ($row = $stmt->fetch()) {
      $attributes[] = $row;
    }

    $stmt = $db->prepare('
        SELECT item_image.image as id, image.path
        FROM item_image
        LEFT JOIN image ON item_image.image = image.id
        WHERE item_image.item = ?
      ');
    $stmt->execute(array($id));

    $images = [];

    while ($row = $stmt->fetch()) {
      $images[] = $row;
    }

    return new Item(
      $item['id'],
      $item['name'],
      $item['description'],
      $item['price'],
      $item['seller'],
      $item['category'],
      $item['status'],
      $item['sold_price'],
      new DateTime($item['creation_date']),
      $item['clicks'],
      $attributes,
      $images,
    );
  }

  static function createItem(PDO $db, array $itemData): ?Item
  {
    $name = $itemData['name'];
    $description = $itemData['description'];
    $price = $itemData['price'];
    $seller = $itemData['seller'];
    $category = $itemData['category'];
    $attributes = $itemData['attributes'];
    $images = $itemData['images'];

    $stmt = $db->prepare('
          INSERT INTO item (name, description, price, seller, category) 
          VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$name, $description, $price, $seller, $category]);

    if ($stmt->rowCount() < 1)
      return null;

    $id = $db->lastInsertId();

    foreach ($attributes as $attribute) {
      $stmt = $db->prepare('
              INSERT INTO item_attributes (item, attribute, value) 
              VALUES (?, ?, ?)');
      $stmt->execute([$id, $attribute['id'], $attribute['value']]);
    }

    foreach ($images as $image) {
      $db->beginTransaction();

      try {
        // Insert the image into the 'image' table
        $stmt = $db->prepare('
                INSERT INTO image (path)
                VALUES (?)');
        $stmt->execute([$image['path']]);

        // Get the last inserted image ID
        $imageId = $db->lastInsertId();

        // Insert the association into the 'item_image' table
        $stmt = $db->prepare('
                INSERT INTO item_image (item, image)
                VALUES (?, ?)');
        $stmt->execute([$id, $imageId]);

        $db->commit();
      } catch (PDOException $e) {
        $db->rollBack();
      }
    }

    return self::getItem($db, (int) $id);
  }

  public function increaseNumberOfClicks(PDO $db): void
  {
    $stmt = $db->prepare('
            UPDATE item
            SET clicks = clicks + 1
            WHERE id = ?');
    $stmt->execute([$this->id]);

    $this->clicks++;
  }
}
?>