<?php
declare(strict_types=1);

class Category
{
  public int $id;
  public string $name;
  public array $attributes = [];

  public function __construct(
    int $id,
    string $name,
    array $attributes,
  ) {
    $this->id = $id;
    $this->name = $name;
    $this->attributes = $attributes;
  }

  static function getAllCategories(PDO $db): array
  {
    $categories = [];

    $stmt = $db->prepare('
        SELECT id
        FROM category
      ');
    $stmt->execute();

    while ($row = $stmt->fetch()) {
      $categories[$row['id']] = Category::getCategory($db, $row['id']);
    }

    return $categories;
  }

  static function getCategory(PDO $db, int $id): ?Category
  {
    $stmt = $db->prepare('
        SELECT id, name
        FROM category
        WHERE id = ?
      ');
    $stmt->execute(array($id));

    $category = $stmt->fetch();

    if (!$category)
      return null;

    $stmt = $db->prepare('
        SELECT attribute as id, attribute.name, attribute.type
        FROM category_attributes
        LEFT JOIN attribute ON category_attributes.attribute = attribute.id
        WHERE category = ?
      ');
    $stmt->execute(array($id));

    $attributes = [];

    while ($row = $stmt->fetch()) {
      if ($row['type'] == 'enum') {
        $values = [];

        $stmt2 = $db->prepare('
            SELECT id, value
            FROM attribute_values
            WHERE attribute_values.attribute = ?
          ');
        $stmt2->execute(array($row['id']));

        while ($row2 = $stmt2->fetch()) {
          $values[] = $row2;
        }

        $row['values'] = $values;
      }
      $attributes[$row['id']] = $row;
    }

    return new Category(
      $category['id'],
      $category['name'],
      $attributes
    );
  }

  static function createCategory(PDO $db, array $categoryData): ?Category
  {
    $name = $categoryData['name'];
    $attributes = $categoryData['attributes'];

    $stmt = $db->prepare('
          INSERT INTO Category (name)
          VALUES (?)');
    $stmt->execute([$name]);

    if ($stmt->rowCount() < 1)
      return null;

    $id = $db->lastInsertId();

    foreach ($attributes as $attribute) {
      $db->beginTransaction();

      try {
        $stmt = $db->prepare('
                INSERT INTO attribute (name, type)
                VALUES (?, ?)');
        $stmt->execute([$attribute['name'], $attribute['type']]);

        $attribute_id = $db->lastInsertId();

        if ($attribute['type'] == 'enum') {
          foreach ($attribute['values'] as $value) {
            $stmt = $db->prepare('
                INSERT INTO attribute_values (attribute, value)
                VALUES (?, ?)');
            $stmt->execute([$attribute_id, $value]);
          }
        }

        $stmt = $db->prepare('
                INSERT INTO category_attributes (category, attribute) 
                VALUES (?, ?)');
        $stmt->execute([$id, $attribute_id]);

        $db->commit();
      } catch (PDOException $e) {
        $db->rollBack();
        throw $e;
      }
    }

    return self::getCategory($db, (int) $id);
  }
}
?>