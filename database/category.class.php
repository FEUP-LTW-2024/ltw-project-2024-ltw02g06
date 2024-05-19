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

        $attributeId = $db->lastInsertId();

        if ($attribute['type'] == 'enum') {
          foreach ($attribute['values'] as $value) {
            $stmt = $db->prepare('
                INSERT INTO attribute_values (attribute, value)
                VALUES (?, ?)');
            $stmt->execute([$attributeId, $value]);
          }
        }

        $stmt = $db->prepare('
                INSERT INTO category_attributes (category, attribute) 
                VALUES (?, ?)');
        $stmt->execute([$id, $attributeId]);

        $db->commit();
      } catch (PDOException $e) {
        $db->rollBack();
        throw $e;
      }
    }

    return self::getCategory($db, (int) $id);
  }

  static function updateCategory(PDO $db, array $categoryData)
  {
    try {
      $category = Category::getCategory($db, (int) $categoryData['id']);

      $id = $category->id;
      if ($id == 1)
        return;
      $currentAttributes = $category->attributes;
      $newAttributes = $categoryData['attributes'];

      foreach ($currentAttributes as $attribute) {
        $attributeExists = false;
        foreach ($newAttributes as $newAttribute) {
          if ($attribute['id'] == $newAttribute['id']) {
            $attributeExists = true;
            if ($attribute['type'] == 'enum') {
              foreach ($attribute['values'] as $currentValue) {
                $valueExists = false;
                foreach ($newAttribute['values'] as $newValue) {
                  if ($currentValue['id'] == $newValue['id']) {
                    $valueExists = true;
                    break;
                  }
                }
                if (!$valueExists) {
                  $stmt = $db->prepare('
                                DELETE FROM attribute_values
                                WHERE id = ?
                            ');
                  $stmt->execute([$currentValue['id']]);

                  $stmt = $db->prepare('
                                DELETE FROM item_attributes
                                WHERE attribute = ?
                                AND value = ?
                            ');
                  $stmt->execute([$attribute['id'], $currentValue['value']]);
                }
              }
            }
            break;
          }
        }
        if (!$attributeExists) {
          $stmt = $db->prepare('
                DELETE FROM attribute
                WHERE id = ?
            ');
          $stmt->execute([$attribute['id']]);
        }
      }

      foreach ($newAttributes as $attribute) {
        if ($attribute['id'] == -1) {
          $db->beginTransaction();

          try {
            $stmt = $db->prepare('
                INSERT INTO attribute (name, type)
                VALUES (?, ?)');
            $stmt->execute([$attribute['name'], $attribute['type']]);

            $attributeId = $db->lastInsertId();

            if ($attribute['type'] == 'enum') {
              foreach ($attribute['values'] as $value) {
                $stmt = $db->prepare('
                INSERT INTO attribute_values (attribute, value)
                VALUES (?, ?)');
                $stmt->execute([$attributeId, $value['value']]);
              }
            }

            $stmt = $db->prepare('
                INSERT INTO category_attributes (category, attribute) 
                VALUES (?, ?)');
            $stmt->execute([$id, $attributeId]);

            $db->commit();
          } catch (PDOException $e) {
            $db->rollBack();
            throw $e;
          }
        } else {
          if ($attribute['type'] == 'enum') {
            foreach ($attribute['values'] as $value) {
              if ($value['id'] == -1) {
                $stmt = $db->prepare('
              INSERT INTO attribute_values (attribute, value)
              VALUES (?, ?)');
                $stmt->execute([$attribute['id'], $value['value']]);
              }
            }
          }
        }
      }
    } catch (Exception $e) {
      throw $e;
    }
  }

  static function deleteCategory(PDO $db, int $id)
  {
    try {
      $stmt = $db->prepare('
                UPDATE item
                SET category = 1
                WHERE category = ?
            ');
      $stmt->execute([$id]);

      $stmt = $db->prepare('
            DELETE FROM attribute
            WHERE id IN (
                SELECT attribute
                FROM category_attributes
                WHERE category = ?
            )
        ');
      $stmt->execute([$id]);

      $stmt = $db->prepare('
                DELETE FROM category
                WHERE id = ?
            ');
      $stmt->execute([$id]);

    } catch (Exception $e) {
      throw $e;
    }
  }
}
?>