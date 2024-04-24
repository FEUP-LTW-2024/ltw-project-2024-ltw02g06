<?php
declare(strict_types=1);

require_once (__DIR__ . '/../database/category.class.php');

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
      $attributes[$row['id']] = $row;
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

  static function createItem(PDO $db, array $item_data): ?Item
  {
    $name = $item_data['name'];
    $description = $item_data['description'];
    $price = $item_data['price'];
    $seller = $item_data['seller'];
    $category = $item_data['category'];
    $attributes = $item_data['attributes'];
    $images = $item_data['images'];

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

  static function updateItemAttributes(PDO $db, $item, array $attributes, Category $new_category, array $new_attributes)
  {
    if ($new_category->id == $item->category) {
      // Update attributes if the category matches
      foreach ($new_attributes as $index => $new_value) {
        if (!$attributes[$index])
          throw new Error("Attribute does not exist.");

        if ($attributes['type'] == 'enum') {
          // Check if the value is valid for enum attributes
          $stmt = $db->prepare('
                  SELECT value FROM attribute_values
                  WHERE attribute = ?');
          $stmt->execute([$index]);
          $possible_values = $stmt->fetchAll(PDO::FETCH_COLUMN);

          if (!in_array($new_value, $possible_values))
            throw new Error("Invalid value for enum attribute.");
        }

        // Update attributes
        $stmt = $db->prepare('
                UPDATE item_attributes 
                SET value = ? 
                WHERE item = ? AND attribute = ?');
        $stmt->execute([$new_value, $item->id, $index]);
      }
    } else {
      // Update item category
      $stmt = $db->prepare('
              UPDATE item
              SET category = ?
              WHERE id = ?');
      $stmt->execute([(int) $new_category->id, $item->id]);

      // Remove all existing attributes of the item
      $stmt = $db->prepare('
              DELETE FROM item_attributes
              WHERE item = ?');
      $stmt->execute([$item->id]);

      // Insert new attributes
      foreach ($new_attributes as $index => $new_value) {
        if (!$attributes[$index])
          throw new Error("Attribute does not exist.");


        if ($attributes['type'] == 'enum') {
          // Check if the value is valid for enum attributes
          $stmt = $db->prepare('
                  SELECT value FROM attribute_values 
                  WHERE attribute = ?');
          $stmt->execute([$index]);
          $possible_values = $stmt->fetchAll(PDO::FETCH_COLUMN);

          if (!in_array($new_value, $possible_values)) {
            throw new Error("Invalid value for enum attribute.");
          }
        }

        // Insert new attributes
        $stmt = $db->prepare('
                INSERT INTO item_attributes (item, attribute, value)
                VALUES (?, ?, ?)');
        $stmt->execute([$item->id, $index, $new_value]);
      }
    }
  }

  static function updateItem(PDO $db, array $item_data): ?Item
  {
    $item = Item::getItem($db, (int) $item_data['id']);
    $categories = Category::getAllCategories($db);

    $category = $categories[$item_data['category']];
    $attributes = $category->attributes;

    $db->beginTransaction();

    try {
      Item::updateItemAttributes($db, $item, $attributes, $category, $item_data['attributes']);

      // Update item info
      $stmt = $db->prepare('
              UPDATE item
              SET price = ?, name = ?, description = ?
              WHERE id = ?');
      $stmt->execute([(float) $item_data['price'], trim($item_data['name']), trim($item_data['description']), $item->id]);

      $db->commit();
    } catch (PDOException $e) {
      $db->rollBack();
      throw $e;
    }

    // Update item images
    $stmt = $db->prepare('
              SELECT id, path 
              FROM item_image
              LEFT JOIN image ON item_image.image = image.id
              WHERE item_image.item = ?');
    $stmt->execute([$item->id]);
    $existing_images = $stmt->fetchAll();

    $stmt = $db->prepare('
              SELECT image as id 
              FROM item_image
              WHERE item = ?');
    $stmt->execute([$item->id]);
    $images = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($existing_images as $existing_image) {
      $still_item_image = false;
      foreach ($item_data['images'] as $index => $image) {
        if ($existing_image['id'] == $index) {
          $still_item_image = true;
          break;
        }
      }
      if (!$still_item_image) {
        $stmt = $db->prepare('
                  DELETE FROM image 
                  WHERE id = ?');
        $stmt->execute([$existing_image['id']]);

        $image_path = './../' . $existing_image['path'];
        if (file_exists($image_path))
          unlink($image_path);
      }
    }

    foreach ($item_data['images'] as $index => $image) {

      if (in_array($index, $images))
        continue;

      $db->beginTransaction();
      try {
        list(, $base64_data) = explode(';', $image);
        list(, $base64_data) = explode(',', $base64_data);
        $image_data = base64_decode($base64_data);
        $filename = generateUniqueFilename('.png');

        file_put_contents('./../database/files/' . $filename, $image_data);

        $stmt = $db->prepare("
                INSERT INTO image (path)
                VALUES (?)");
        $stmt->execute(["database/files/" . $filename]);

        $image_id = $db->lastInsertId();

        $stmt = $db->prepare("
                INSERT INTO item_image (item, image)
                VALUES (?, ?)");
        $stmt->execute([$item->id, $image_id]);

        $db->commit();
      } catch (PDOException $e) {
        $db->rollBack();
      }
    }

    return Item::getItem($db, (int) $item_data['id']);
  }

  static function deleteItem(PDO $db, int $id): void
  {
    try {
      $stmt = $db->prepare('
            DELETE FROM item
            WHERE id = ?
        ');
      $stmt->execute([$id]);
    } catch (PDOException $e) {
      throw $e;
    }
  }
}

// Auxiliar function
function generateUniqueFilename(string $extension)
{
  $new_filename = uniqid() . $extension;

  // Check if the filename already exists, if so, generate a new one until it's unique
  while (file_exists("/../database/files/$new_filename")) {
    $new_filename = uniqid() . $extension;
  }

  return $new_filename;
}
?>