<?php
declare(strict_types=1);

class Item
{
  public int $id;
  public string $name;

  // TODO - Add other params

  public function __construct(int $id, string $name)
  {
    $this->id = $id;
    $this->title = $name;
  }

  static function getArtistAlbums(PDO $db, int $id): array
  {
    // TODO - Change query
    $stmt = $db->prepare('
        SELECT id, name
        FROM item
        WHERE sellerId = ?
        GROUP BY id
      ');
    $stmt->execute(array($id));

    $items = array();

    while ($item = $stmt->fetch()) {
      $items[] = new Item(
        $item['id'],
        $item['name']
      );
    }

    return $items;
  }

  static function getItem(PDO $db, int $id): ?Item
  {
    // TODO - Change query
    $stmt = $db->prepare('
        SELECT id, name
        FROM item
        WHERE id = ?
      ');
    $stmt->execute(array($id));

    $item = $stmt->fetch();

    if (!$item)
      return null;

    return new Item(
      $item['id'],
      $item['name']
    );
  }

  function save(PDO $db)
  {
    // TODO - Change query
    $stmt = $db->prepare('
        UPDATE item SET name = ?
        WHERE id = ?
      ');
    $stmt->execute(array($this->name, $this->id));
  }

}
?>