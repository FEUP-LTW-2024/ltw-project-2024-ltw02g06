<?php
declare(strict_types=1);

require_once (__DIR__ . '/../database/category.class.php');
require_once (__DIR__ . '/../database/user.class.php');
require_once (__DIR__ . '/../database/utils.php');

class Item
{
  public int $id;
  public string $name;
  public string $description;
  public float $price;
  public int $seller;
  public ?int $buyer;
  public int $category;
  public string $status;
  public ?float $soldPrice;
  public DateTime $creationDate;
  public int $clicks;
  public array $attributes = [];
  public array $images = [];

  public function __construct(
    int $id,
    string $name,
    string $description,
    float $price,
    int $seller,
    ?int $buyer,
    int $category,
    string $status,
    ?float $soldPrice,
    DateTime $creationDate,
    int $clicks,
    array $attributes,
    array $images
  ) {
    $this->id = $id;
    $this->name = $name;
    $this->description = $description;
    $this->price = $price;
    $this->seller = $seller;
    $this->buyer = $buyer;
    $this->category = $category;
    $this->status = $status;
    $this->soldPrice = $soldPrice;
    $this->creationDate = $creationDate;
    $this->clicks = $clicks;
    $this->attributes = $attributes;
    $this->images = $images;
  }

  static function getItem(PDO $db, int $id): ?Item
  {
    $stmt = $db->prepare('
        SELECT id, name, description, price, seller, buyer, category, status, sold_price, creation_date, clicks
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
      $item['buyer'],
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
        $imageData = base64_decode($base64_data);
        $filename = generateUniqueFilename('.png');

        file_put_contents('./../database/files/' . $filename, $imageData);

        $stmt = $db->prepare("
                INSERT INTO image (path)
                VALUES (?)");
        $stmt->execute(["database/files/" . $filename]);

        $imageId = $db->lastInsertId();

        $stmt = $db->prepare("
                INSERT INTO item_image (item, image)
                VALUES (?, ?)");
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

  static function updateItemAttributes(PDO $db, $item, array $attributes, Category $newCategory, array $newAttributes)
  {
    if ($newCategory->id == $item->category) {
      // Update attributes if the category matches
      foreach ($newAttributes as $index => $newValue) {
        if (!$attributes[$index])
          throw new Error("Attribute does not exist.");

        if ($attributes['type'] == 'enum') {
          // Check if the value is valid for enum attributes
          $stmt = $db->prepare('
                  SELECT value FROM attribute_values
                  WHERE attribute = ?');
          $stmt->execute([$index]);
          $possibleValues = $stmt->fetchAll(PDO::FETCH_COLUMN);

          if (!in_array($newValue, $possibleValues))
            throw new Error("Invalid value for enum attribute.");
        }

        // Update attributes
        $stmt = $db->prepare('
                UPDATE item_attributes 
                SET value = ? 
                WHERE item = ? AND attribute = ?');
        $stmt->execute([$newValue, $item->id, $index]);
      }
    } else {
      // Update item category
      $stmt = $db->prepare('
              UPDATE item
              SET category = ?
              WHERE id = ?');
      $stmt->execute([(int) $newCategory->id, $item->id]);

      // Remove all existing attributes of the item
      $stmt = $db->prepare('
              DELETE FROM item_attributes
              WHERE item = ?');
      $stmt->execute([$item->id]);

      // Insert new attributes
      foreach ($newAttributes as $index => $newValue) {
        if (!$attributes[$index])
          throw new Error("Attribute does not exist.");


        if ($attributes['type'] == 'enum') {
          // Check if the value is valid for enum attributes
          $stmt = $db->prepare('
                  SELECT value FROM attribute_values 
                  WHERE attribute = ?');
          $stmt->execute([$index]);
          $possibleValues = $stmt->fetchAll(PDO::FETCH_COLUMN);

          if (!in_array($newValue, $possibleValues)) {
            throw new Error("Invalid value for enum attribute.");
          }
        }

        // Insert new attributes
        $stmt = $db->prepare('
                INSERT INTO item_attributes (item, attribute, value)
                VALUES (?, ?, ?)');
        $stmt->execute([$item->id, $index, $newValue]);
      }
    }
  }

  static function updateItem(PDO $db, array $itemData): ?Item
  {
    $item = Item::getItem($db, (int) $itemData['id']);
    $categories = Category::getAllCategories($db);

    $category = $categories[$itemData['category']];
    $attributes = $category->attributes;

    $db->beginTransaction();

    try {
      Item::updateItemAttributes($db, $item, $attributes, $category, $itemData['attributes']);

      // Update item info
      $stmt = $db->prepare('
              UPDATE item
              SET price = ?, name = ?, description = ?
              WHERE id = ?');
      $stmt->execute([(float) $itemData['price'], trim($itemData['name']), trim($itemData['description']), $item->id]);

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
    $existingImages = $stmt->fetchAll();

    $stmt = $db->prepare('
              SELECT image as id 
              FROM item_image
              WHERE item = ?');
    $stmt->execute([$item->id]);
    $images = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($existingImages as $existingImage) {
      $stillItemImage = false;
      foreach ($itemData['images'] as $index => $image) {
        if ($existingImage['id'] == $index) {
          $stillItemImage = true;
          break;
        }
      }
      if (!$stillItemImage) {
        $stmt = $db->prepare('
                  DELETE FROM image 
                  WHERE id = ?');
        $stmt->execute([$existingImage['id']]);

        $imagePath = dirname(__FILE__) . '/../' . $image['path'];
        if (file_exists($imagePath))
          unlink($imagePath);
      }
    }

    foreach ($itemData['images'] as $index => $image) {

      if (in_array($index, $images))
        continue;

      $db->beginTransaction();
      try {
        list(, $base64_data) = explode(';', $image);
        list(, $base64_data) = explode(',', $base64_data);
        $imageData = base64_decode($base64_data);
        $filename = generateUniqueFilename('.png');

        file_put_contents(dirname(__FILE__) . '/../database/files/' . $filename, $imageData);

        $stmt = $db->prepare("
                INSERT INTO image (path)
                VALUES (?)");
        $stmt->execute(["database/files/" . $filename]);

        $imageId = $db->lastInsertId();

        $stmt = $db->prepare("
                INSERT INTO item_image (item, image)
                VALUES (?, ?)");
        $stmt->execute([$item->id, $imageId]);

        $db->commit();
        $db->exec('PRAGMA foreign_keys = ON');
      } catch (PDOException $e) {
        $db->rollBack();
      }
    }

    return Item::getItem($db, (int) $itemData['id']);
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

      $imagePath = dirname(__FILE__) . '/../' . $image['path'];
      if (file_exists($imagePath))
        unlink($imagePath);
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

  static function getAllItems(PDO $db, ?int $userId, ?int $sellerId, int $page, int $itemsPerPage, array $search, bool $active = true): array
  {
    $nameSearch = isset($search['search']) ? $search['search'] : null;
    $locationSearch = isset($search['location']) ? $search['location'] : null;
    $order = isset($search['order']) ? $search['order'] : null;
    $priceFrom = isset($search['price']['from']) ? floatval($search['price']['from']) : null;
    $priceTo = isset($search['price']['to']) ? floatval($search['price']['to']) : null;
    $category = isset($search['category']) ? intval($search['category']) : null;
    $attributes = isset($search['attributes']) ? $search['attributes'] : [];

    $offset = ($page - 1) * $itemsPerPage;

    $query = '
    SELECT item.id
    FROM item
    LEFT JOIN user ON item.seller = user.id ';

    if ($active)
      $query .= ' WHERE item.status = "active" ';
    else
      $query .= ' WHERE item.status LIKE "%" ';

    $whereConditions = [];

    if ($category !== null) {
      $query .= ' AND item.category = :category ';
      $whereConditions[':category'] = $category;
    }

    if ($nameSearch !== null) {
      $nameSearchModified = str_replace([' ', ','], '%', $nameSearch);
      $query .= ' AND (item.name LIKE :name_search OR item.description LIKE :name_search) ';
      $whereConditions[':name_search'] = '%' . $nameSearchModified . '%';
    }

    if ($locationSearch !== null) {
      $locationSearchModified = str_replace([' ', ','], '%', $locationSearch);

      $query .= ' AND (user.city LIKE :location_search 
                  OR user.state LIKE :location_search 
                  OR user.country LIKE :location_search
                  OR (user.city || " " || user.state || " " || user.country) LIKE :location_search)';
      $whereConditions[':location_search'] = '%' . $locationSearchModified . '%';
    }

    if ($priceFrom !== null) {
      $query .= ' AND item.price >= :price_from ';
      $whereConditions[':price_from'] = $priceFrom;
    }

    if ($priceTo !== null) {
      $query .= ' AND item.price <= :price_to ';
      $whereConditions[':price_to'] = $priceTo;
    }

    if ($sellerId !== null) {
      $query .= ' AND item.seller = :seller_id ';
      $whereConditions[':seller_id'] = $sellerId;
    }

    foreach ($attributes as $attributeId => $attributeValue) {
      $paramId = ':attributeId' . $attributeId;
      $paramValue = ':attributeValue' . $attributeId;

      $stmt = $db->prepare("SELECT type FROM attribute WHERE id = ?");
      $stmt->execute([$attributeId]);
      $attributeType = $stmt->fetchColumn();

      if ($attributeType == 'int' || $attributeType == 'real') {
        $query .= " AND item.id IN (
                    SELECT item_attributes.item
                    FROM item_attributes
                    WHERE item_attributes.attribute = $paramId ";

        if (isset($attributeValue['from'])) {
          $query .= "AND CAST(item_attributes.`value` AS REAL) >= CAST(:fromAttributeValue$attributeId AS REAL) ";
          $whereConditions[":fromAttributeValue$attributeId"] = $attributeValue['from'];
        }

        if (isset($attributeValue['to'])) {
          $query .= "AND CAST(item_attributes.`value` AS REAL) <= CAST(:toAttributeValue$attributeId AS REAL)";
          $whereConditions[":toAttributeValue$attributeId"] = $attributeValue['to'];
        }

        $query .= ")";
        $whereConditions[$paramId] = $attributeId;
      } else {
        $query .= " AND item.id IN (
                    SELECT item_attributes.item
                    FROM item_attributes
                    WHERE item_attributes.attribute = $paramId
                    AND item_attributes.`value` LIKE $paramValue
                ) ";
        $whereConditions[$paramId] = $attributeId;
        $whereConditions[$paramValue] = "%" . $attributeValue . "%";
      }
    }

    $query .= ' ORDER BY ';

    if ($order === 'price:asc')
      $query .= ' item.price ASC ';
    elseif ($order === 'price:desc')
      $query .= ' item.price DESC ';
    elseif ($order === 'createdAt:asc')
      $query .= ' item.creation_date ASC ';
    elseif ($order === 'createdAt:desc')
      $query .= ' item.creation_date DESC ';
    else
      $query .= 'item.clicks DESC';

    $query .= ' LIMIT :limit OFFSET :offset ';

    $stmt = $db->prepare($query);

    $whereConditions[':limit'] = $itemsPerPage;
    $whereConditions[':offset'] = $offset;

    $stmt->execute($whereConditions);

    $itemsId = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $items = [];
    foreach ($itemsId as $itemId) {
      $item = Item::getItem($db, $itemId);
      $inCart = $userId ? User::isItemInCart($db, $userId, $itemId) : false;
      $inWishlist = $userId ? User::isItemInWishlist($db, $userId, $itemId) : false;
      $seller = User::getUser($db, $item->seller);
      $buyer = $item->buyer ? User::getUser($db, $item->buyer) : null;
      $items[] = [
        'item' => $item,
        'inCart' => $inCart,
        'inWishlist' => $inWishlist,
        'seller' => $seller,
        'buyer' => $buyer,
      ];
    }

    return $items;
  }

  static function getItemsTotal(PDO $db, ?int $sellerId, array $search, bool $active = true): int
  {
    $nameSearch = isset($search['search']) ? $search['search'] : null;
    $locationSearch = isset($search['location']) ? $search['location'] : null;
    $priceFrom = isset($search['price']['from']) ? floatval($search['price']['from']) : null;
    $priceTo = isset($search['price']['to']) ? floatval($search['price']['to']) : null;
    $category = isset($search['category']) ? intval($search['category']) : null;
    $attributes = isset($search['attributes']) ? $search['attributes'] : [];

    $query = '
    SELECT COUNT(item.id) AS total
    FROM item
    LEFT JOIN user ON item.seller = user.id ';

    if ($active)
      $query .= ' WHERE item.status = "active" ';
    else
      $query .= ' WHERE item.status LIKE "%" ';

    $whereConditions = [];

    if ($category !== null) {
      $query .= ' AND item.category = :category ';
      $whereConditions[':category'] = $category;
    }

    if ($nameSearch !== null) {
      $nameSearchModified = str_replace([' ', ','], '%', $nameSearch);
      $query .= ' AND (item.name LIKE :name_search OR item.description LIKE :name_search) ';
      $whereConditions[':name_search'] = '%' . $nameSearchModified . '%';
    }

    if ($locationSearch !== null) {
      $locationSearchModified = str_replace([' ', ','], '%', $locationSearch);

      $query .= ' AND (user.city LIKE :location_search 
                  OR user.state LIKE :location_search 
                  OR user.country LIKE :location_search
                  OR (user.city || " " || user.state || " " || user.country) LIKE :location_search)';
      $whereConditions[':location_search'] = '%' . $locationSearchModified . '%';
    }

    if ($priceFrom !== null) {
      $query .= ' AND item.price >= :price_from ';
      $whereConditions[':price_from'] = $priceFrom;
    }

    if ($priceTo !== null) {
      $query .= ' AND item.price <= :price_to ';
      $whereConditions[':price_to'] = $priceTo;
    }

    if ($sellerId !== null) {
      $query .= ' AND item.seller = :seller_id ';
      $whereConditions[':seller_id'] = $sellerId;
    }

    foreach ($attributes as $attributeId => $attributeValue) {
      $paramId = ':attributeId' . $attributeId;
      $paramValue = ':attributeValue' . $attributeId;

      $stmt = $db->prepare("SELECT type FROM attribute WHERE id = ?");
      $stmt->execute([$attributeId]);
      $attributeType = $stmt->fetchColumn();

      if ($attributeType == 'int' || $attributeType == 'real') {
        $query .= " AND item.id IN (
                        SELECT item_attributes.item
                        FROM item_attributes
                        WHERE item_attributes.attribute = $paramId ";

        if (isset($attributeValue['from'])) {
          $query .= "AND CAST(item_attributes.`value` AS REAL) >= CAST(:fromAttributeValue$attributeId AS REAL) ";
          $whereConditions[":fromAttributeValue$attributeId"] = $attributeValue['from'];
        }

        if (isset($attributeValue['to'])) {
          $query .= "AND CAST(item_attributes.`value` AS REAL) <= CAST(:toAttributeValue$attributeId AS REAL) ";
          $whereConditions[":toAttributeValue$attributeId"] = $attributeValue['to'];
        }

        $query .= ")";
        $whereConditions[$paramId] = $attributeId;
      } else {
        $query .= " AND item.id IN (
                    SELECT item_attributes.item
                    FROM item_attributes
                    WHERE item_attributes.attribute = $paramId
                    AND item_attributes.`value` LIKE $paramValue
                ) ";
        $whereConditions[$paramId] = $attributeId;
        $whereConditions[$paramValue] = "%" . $attributeValue . "%";
      }
    }

    $stmt = $db->prepare($query);

    $stmt->execute($whereConditions);

    $total = $stmt->fetchColumn();

    return $total;
  }

  static function buyItem(PDO $db, int $buyer, int $id, float $soldPrice): ?Item
  {
    try {
      // Update item info
      $stmt = $db->prepare('
              UPDATE item
              SET sold_price = ?, 
                  status = "to send", 
                  buyer = ?
              WHERE id = ?');
      $stmt->execute([$soldPrice, $buyer, $id]);

      return Item::getItem($db, $id);
    } catch (PDOException $e) {
      $db->rollBack();
      throw $e;
    }
  }

  static function sendItem(PDO $db, int $id): ?Item
  {
    try {
      $stmt = $db->prepare('
              UPDATE item
              SET status = "sold"
              WHERE id = ?');
      $stmt->execute([$id]);

      return Item::getItem($db, $id);
    } catch (PDOException $e) {
      $db->rollBack();
      throw $e;
    }
  }
}
?>