<?php
declare(strict_types=1);

require_once (__DIR__ . '/../database/category.class.php');
require_once (__DIR__ . '/../database/user.class.php');

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
    $stmt->execute([$name, $description, (float) $price, (int) $seller, $category]);

    if ($stmt->rowCount() < 1)
      return null;

    $id = $db->lastInsertId();

    foreach ($attributes as $index => $attribute) {
      $stmt = $db->prepare('
              INSERT INTO item_attributes (item, attribute, value) 
              VALUES (?, ?, ?)');
      $stmt->execute([$id, $index, $attribute]);
    }

    foreach ($images as $image) {

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
        $stmt->execute([$id, $image_id]);

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

        $image_path = dirname(__FILE__) . '/../' . $image['path'];
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

        file_put_contents(dirname(__FILE__) . '/../database/files/' . $filename, $image_data);

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
        $db->exec('PRAGMA foreign_keys = ON');
      } catch (PDOException $e) {
        $db->rollBack();
      }
    }

    return Item::getItem($db, (int) $item_data['id']);
  }

  static function deleteItem(PDO $db, int $id): void
  {
    // Fetch images associated with the item
    $stmt = $db->prepare('
    SELECT id, path 
    FROM item_image
    LEFT JOIN image ON item_image.image = image.id
    WHERE item_image.item = ?');
    $stmt->execute([$id]);
    $images = $stmt->fetchAll();

    foreach ($images as $image) {
      $stmt = $db->prepare('
        DELETE FROM image 
        WHERE id = ?');
      $stmt->execute([$image['id']]);

      // correct path changes depending on the function call this function
      $image_path = dirname(__FILE__) . '/../' . $image['path'];
      if (file_exists($image_path))
        unlink($image_path);
    }

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

  static function getAllItems(PDO $db, ?int $user_id, int $page, int $items_per_page, array $search): array
  {
    $name_search = isset($search['search']) ? $search['search'] : null;
    $location_search = isset($search['location']) ? $search['location'] : null;
    $order = isset($search['order']) ? $search['order'] : null;
    $price_from = isset($search['price']['from']) ? floatval($search['price']['from']) : null;
    $price_to = isset($search['price']['to']) ? floatval($search['price']['to']) : null;
    $category = isset($search['category']) ? intval($search['category']) : null;
    $attributes = isset($search['attributes']) ? $search['attributes'] : [];

    $offset = ($page - 1) * $items_per_page;

    $query = '
    SELECT item.id
    FROM item
    LEFT JOIN user ON item.seller = user.id ';

    $whereConditions = [];

    if ($category !== null) {
      $query .= ' WHERE item.category = :category ';
      $whereConditions[':category'] = $category;
    }

    if ($name_search !== null || $location_search !== null || $price_from !== null || $price_to !== null || !empty($attributes)) {
      $query .= !empty($whereConditions) ? ' ' : ' WHERE ';
    }

    if ($name_search !== null) {
      $query .= ' (item.name LIKE :name_search OR item.description LIKE :name_search) ';
      $whereConditions[':name_search'] = '%' . $name_search . '%';
    }

    if ($location_search !== null) {
      if ($category !== null || $name_search !== null) {
        $query .= ' AND ';
      }
      $query .= ' ((user.city LIKE :location_search OR user.state LIKE :location_search OR user.country LIKE :location_search)
                  OR (user.city || "%" || user.state || "%" || user.country || "%" LIKE :location_search)) ';
      $whereConditions[':location_search'] = '%' . $location_search . '%';
    }

    if ($price_from !== null) {
      if ($category !== null || $name_search !== null || $location_search !== null) {
        $query .= ' AND ';
      }
      $query .= ' item.price >= :price_from ';
      $whereConditions[':price_from'] = $price_from;
    }

    if ($price_to !== null) {
      if ($category !== null || $name_search !== null || $location_search !== null || $price_from !== null) {
        $query .= ' AND ';
      }
      $query .= ' item.price <= :price_to ';
      $whereConditions[':price_to'] = $price_to;
    }

    foreach ($attributes as $attributeId => $attributeValue) {
      if ($category !== null || $name_search !== null || $location_search !== null || $price_from !== null || $price_to !== null || !empty($whereConditions)) {
        $query .= ' AND ';
      }
      $paramId = ':attributeId' . $attributeId;
      $paramValue = ':attributeValue' . $attributeId;
      $query .= " item.id IN (
                    SELECT item_attributes.item
                    FROM item_attributes
                    WHERE item_attributes.attribute = $paramId
                    AND item_attributes.`value` LIKE $paramValue
                ) ";
      $whereConditions[$paramId] = $attributeId;
      $whereConditions[$paramValue] = "%" . $attributeValue . "%";
    }

    $query .= ' ORDER BY ';

    if ($order === 'price:asc')
      $query .= ' item.price ASC ';
    elseif ($order === 'price:desc')
      $query .= ' item.price DESC ';
    elseif ($order === 'created_at:asc')
      $query .= ' item.creation_date ASC ';
    elseif ($order === 'created_at:desc')
      $query .= ' item.creation_date DESC ';
    else
      $query .= 'item.clicks DESC';

    $query .= ' LIMIT :limit OFFSET :offset ';

    $stmt = $db->prepare($query);

    foreach ($whereConditions as $param => $value) {
      $stmt->bindParam($param, $value);
      if (is_int($value)) {
        $stmt->bindParam($param, $value, PDO::PARAM_INT);
      } else {
        $stmt->bindParam($param, $value, PDO::PARAM_STR);
      }
    }

    $whereConditions[':limit'] = $items_per_page;
    $whereConditions[':offset'] = $offset;

    $stmt->execute($whereConditions);

    $items_id = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $items = [];
    foreach ($items_id as $item_id) {
      $item = Item::getItem($db, $item_id);
      $in_cart = $user_id ? User::isItemInCart($db, $user_id, $item_id) : false;
      $in_wishlist = $user_id ? User::isItemInWishlist($db, $user_id, $item_id) : false;
      $seller = User::getUser($db, $item->seller);
      $items[] = [
        'item' => $item,
        'in_cart' => $in_cart,
        'in_wishlist' => $in_wishlist,
        'seller' => $seller
      ];
    }

    return $items;
  }

  static function getItemsTotal(PDO $db, array $search): int
  {
    $name_search = isset($search['search']) ? $search['search'] : null;
    $location_search = isset($search['location']) ? $search['location'] : null;
    $price_from = isset($search['price']['from']) ? floatval($search['price']['from']) : null;
    $price_to = isset($search['price']['to']) ? floatval($search['price']['to']) : null;
    $category = isset($search['category']) ? intval($search['category']) : null;
    $attributes = isset($search['attributes']) ? $search['attributes'] : [];

    $query = '
    SELECT COUNT(item.id) AS total
    FROM item
    LEFT JOIN user ON item.seller = user.id ';

    $whereConditions = [];

    if ($category !== null) {
      $query .= ' WHERE item.category = :category ';
      $whereConditions[':category'] = $category;
    }

    if ($name_search !== null || $location_search !== null || $price_from !== null || $price_to !== null || !empty($attributes)) {
      $query .= !empty($whereConditions) ? '' : ' WHERE ';
    }

    if ($name_search !== null) {
      $query .= ' (item.name LIKE :name_search OR item.description LIKE :name_search) ';
      $whereConditions[':name_search'] = '%' . $name_search . '%';
    }

    if ($location_search !== null) {
      if ($category !== null || $name_search !== null) {
        $query .= ' AND ';
      }
      $query .= ' ((user.city LIKE :location_search OR user.state LIKE :location_search OR user.country LIKE :location_search)
                  OR (user.city || "%" || user.state || "%" || user.country || "%" LIKE :location_search)) ';
      $whereConditions[':location_search'] = '%' . $location_search . '%';
    }

    if ($price_from !== null) {
      if ($category !== null || $location_search !== null) {
        $query .= ' AND ';
      }
      $query .= ' item.price >= :price_from ';
      $whereConditions[':price_from'] = $price_from;
    }

    if ($price_to !== null) {
      if ($category !== null || $name_search !== null || $location_search !== null || $price_from !== null) {
        $query .= ' AND ';
      }
      $query .= ' item.price <= :price_to ';
      $whereConditions[':price_to'] = $price_to;
    }

    foreach ($attributes as $attributeId => $attributeValue) {
      if ($category !== null || $name_search !== null || $location_search !== null || $price_from !== null || $price_to !== null || !empty($whereConditions)) {
        $query .= ' AND ';
      }
      $paramId = ':attributeId' . $attributeId;
      $paramValue = ':attributeValue' . $attributeId;
      $query .= " item.id IN (
                    SELECT item_attributes.item
                    FROM item_attributes
                    WHERE item_attributes.attribute = $paramId
                    AND item_attributes.`value` LIKE $paramValue
                ) ";
      $whereConditions[$paramId] = $attributeId;
      $whereConditions[$paramValue] = "%" . $attributeValue . "%";
    }

    $stmt = $db->prepare($query);

    $stmt->execute($whereConditions);

    $total = $stmt->fetchColumn();

    return $total;
  }
}

// Auxiliar function
function generateUniqueFilename(string $extension)
{
  $new_filename = uniqid() . $extension;

  // Check if the filename already exists, if so, generate a new one until it's unique
  while (file_exists(dirname(__FILE__) . "/../database/files/$new_filename")) {
    $new_filename = uniqid() . $extension;
  }

  return $new_filename;
}
?>