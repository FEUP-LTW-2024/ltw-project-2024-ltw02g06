<?php
declare(strict_types=1);

require_once (__DIR__ . '/../database/utils.php');

class User
{
  public int $id;
  public string $first_name;
  public string $last_name;
  public string $email;
  public ?string $password;
  public string $address;
  public string $city;
  public string $state;
  public string $country;
  public string $zipcode;
  public string $image;
  public bool $admin;
  public DateTime $registration_date;


  public function __construct(
    int $id,
    string $first_name,
    string $last_name,
    string $email,
    string $password,
    string $address,
    string $city,
    string $state,
    string $country,
    string $zipcode,
    string $image,
    bool $admin,
    DateTime $registration_date
  ) {
    $this->id = $id;
    $this->first_name = $first_name;
    $this->last_name = $last_name;
    $this->email = $email;
    $this->password = $password;
    $this->address = $address;
    $this->city = $city;
    $this->state = $state;
    $this->country = $country;
    $this->zipcode = $zipcode;
    $this->image = $image;
    $this->admin = $admin;
    $this->registration_date = $registration_date;
  }

  function name()
  {
    return $this->first_name . ' ' . $this->last_name;
  }

  function save($db)
  {
    $stmt = $db->prepare('
        UPDATE user SET first_name = ?, last_name = ?
        WHERE id = ?
      ');

    $stmt->execute(array($this->first_name, $this->last_name, $this->id));
  }

  static function createUser(PDO $db, string $email, string $password, string $firstName, string $lastName, string $address, string $city, string $state, string $country, string $zipcode): ?User
  {
    $stmt = $db->prepare('
        INSERT INTO user (email, password, first_name, last_name, address, city, state, country, zipcode, admin, registration_date)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?)
    ');
    $stmt->execute([
      $email,
      password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]),
      $firstName,
      $lastName,
      $address,
      $city,
      $state,
      $country,
      $zipcode,
      (new DateTime())->format('Y-m-d H:i:s')
    ]);

    $id = $db->lastInsertId();

    return User::getUser($db, (int) $id);
  }

  static function getUserWithPassword(PDO $db, string $email, string $password): ?User
  {
    $stmt = $db->prepare('
        SELECT user.id, first_name, last_name, email, password, address, city, state, country, zipcode, image.path AS image, admin, registration_date
        FROM user
        LEFT JOIN image ON user.image = image.id
        WHERE lower(email) = ?
      ');

    $stmt->execute(array(strtolower($email)));
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
      return new User(
        $user['id'],
        $user['first_name'],
        $user['last_name'],
        $user['email'],
        $user['password'],
        $user['address'],
        $user['city'],
        $user['state'],
        $user['country'],
        $user['zipcode'],
        $user['image'],
        (bool) $user['admin'],
        new DateTime($user['registration_date'])
      );
    } else
      return null;
  }

  static function getUser(PDO $db, int $id): ?User
  {
    $stmt = $db->prepare('
        SELECT user.id, first_name, last_name, email, password, address, city, state, country, zipcode, image.path AS image, admin, registration_date
        FROM user
        LEFT JOIN image ON user.image = image.id
        WHERE user.id = ?
      ');

    $stmt->execute(array($id));

    if ($user = $stmt->fetch()) {
      return new User(
        $user['id'],
        $user['first_name'],
        $user['last_name'],
        $user['email'],
        $user['password'],
        $user['address'],
        $user['city'],
        $user['state'],
        $user['country'],
        $user['zipcode'],
        $user['image'],
        (bool) $user['admin'],
        new DateTime($user['registration_date'])
      );
    } else
      return null;
  }

  static function getAllUsers(PDO $db, array $search): array
  {
    $searchStr = isset($search['search']) ? $search['search'] : null;

    $stmt = $db->prepare('
        SELECT user.id
        FROM user
        WHERE user.first_name LIKE :search
        OR user.last_name LIKE :search
        OR user.email LIKE :search
        OR (user.city LIKE :search OR user.state LIKE :search OR user.country LIKE :search)
        OR (user.city || "%" || user.state || "%" || user.country || "%" LIKE :search)
        OR user.id LIKE :search
        OR ("#" || user.id) LIKE :search
      ');

    $stmt->execute([':search' => "%" . $searchStr . "%"]);

    $users_id = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $users = [];
    foreach ($users_id as $user_id) {
      $user = User::getUser($db, $user_id);
      $user->password = null;
      $users[] = $user;
    }

    return $users;
  }

  static function updateUser(PDO $db, array $user_data)
  {
    $image = $user_data['new_image'];
    $id = $user_data['id'];

    if ($image) {

      $stmt = $db->prepare('
                  SELECT image.id, image.path 
                  FROM user
                  LEFT JOIN image ON user.image = image.id
                  WHERE user.id = ?');
      $stmt->execute([$id]);

      $current_image = $stmt->fetchAll();

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
                UPDATE user
                SET image = ?
                WHERE id = ?");
        $stmt->execute([$image_id, $id]);

        $db->commit();
      } catch (PDOException $e) {
        $db->rollBack();
      }

      if ($current_image['id'] != "1") {
        $stmt = $db->prepare('
                  DELETE FROM image 
                  WHERE id = ?');
        $stmt->execute([$current_image['id']]);

        $image_path = dirname(__FILE__) . '/../' . $current_image['path'];
        if (file_exists($image_path))
          unlink($image_path);
      }
    }

    $stmt = $db->prepare('
          UPDATE user
          SET first_name = ?,
          last_name = ?,
          email = ?,
          address = ?,
          city = ?,
          state = ?,
          country = ?,
          zipcode = ?
          WHERE id = ?
        ');

    $stmt->execute([
      $user_data['first_name'],
      $user_data['last_name'],
      $user_data['email'],
      $user_data['address'],
      $user_data['city'],
      $user_data['state'],
      $user_data['country'],
      $user_data['zipcode'],
      $user_data['id'],
    ]);
  }

  static function deleteUser(PDO $db, int $user_id)
  {
    try {
      $stmt = $db->prepare('
            DELETE FROM user
            WHERE id = ?
        ');
      $stmt->execute([$user_id]);
    } catch (PDOException $e) {
      throw $e;
    }
  }

  static function updateAdminStatus(PDO $db, array $user_data)
  {
    $stmt = $db->prepare('
          UPDATE user
          SET admin = ?
          WHERE id = ?
        ');

    $stmt->execute([
      $user_data['admin'],
      $user_data['id'],
    ]);
  }

  static function getUserReviews(PDO $db, int $id): ?array
  {
    $stmt = $db->prepare('
        SELECT id, reviewer_user as reviewer, rating, comment, timestamp
        FROM review
        WHERE reviewed_user = ?
      ');
    $stmt->execute(array($id));

    $reviews = [];

    while ($row = $stmt->fetch()) {
      $reviews[] = $row;
    }

    return $reviews;
  }

  static function getWishlist(PDO $db, int $id): array
  {
    $stmt = $db->prepare('
            SELECT item.id
            FROM user_wishlist
            LEFT JOIN item ON item.id = user_wishlist.item
            WHERE user_wishlist.user = ? AND item.status = "active"
          ');
    $stmt->execute([$id]);

    $items_id = $stmt->fetchAll() ?: [];

    $wishlist = [];

    foreach ($items_id as $item_id) {
      $item = Item::getItem($db, $item_id['id']);
      $seller = User::getUser($db, $item->seller);
      $seller->password = null;
      $isItemInCart = User::isItemInCart($db, $id, $item_id['id']);
      $wishlistItem = array(
        'item' => $item,
        'seller' => $seller,
        'isItemInCart' => $isItemInCart
      );
      $wishlist[] = $wishlistItem;
    }

    return $wishlist;
  }

  static function isItemInWishlist(PDO $db, int $id, int $item_id): bool
  {
    $stmt = $db->prepare('
            SELECT COUNT(*) AS count
            FROM user_wishlist
            WHERE user = ? AND item = ?
        ');
    $stmt->execute([$id, $item_id]);

    $result = $stmt->fetch();

    return $result['count'] > 0;
  }

  static function addItemToWishlist(PDO $db, int $id, int $item_id)
  {
    try {
      $stmt = $db->prepare('
          INSERT INTO user_wishlist (item, user)
          VALUES (?, ?)
      ');
      $stmt->execute([$item_id, $id]);
    } catch (PDOException $e) {
      throw $e;
    }
  }

  static function removeItemFromWishlist(PDO $db, int $user_id, int $item_id)
  {
    try {
      $stmt = $db->prepare('
            DELETE FROM user_wishlist
            WHERE item = ? AND user = ?
        ');
      $stmt->execute([$item_id, $user_id]);
    } catch (PDOException $e) {
      throw $e;
    }
  }

  static function getCart(PDO $db, int $id): array
  {
    $stmt = $db->prepare('
            SELECT user_cart.item as item_id, item.name as item_name, item.price as old_price, 
            user_cart.user, user_cart.price as new_price, user_cart.shipping
            FROM user_cart
            LEFT JOIN item ON item.id = user_cart.item
            WHERE user = ? AND item.status = "active"
          ');
    $stmt->execute([$id]);

    $cart = $stmt->fetchAll();

    return $cart ?: [];
  }

  static function isItemInCart(PDO $db, int $id, int $item_id): bool
  {
    $stmt = $db->prepare('
            SELECT COUNT(*) AS count
            FROM user_cart
            WHERE user = ? AND item = ?
        ');
    $stmt->execute([$id, $item_id]);

    $result = $stmt->fetch();

    return $result['count'] > 0;
  }

  static function getCartItem(PDO $db, int $id, int $item_id): array
  {
    $stmt = $db->prepare('
            SELECT item, user, price, shipping
            FROM user_cart
            WHERE user = ? AND item = ?
        ');
    $stmt->execute([$id, $item_id]);

    $item = $stmt->fetch();

    return $item ? $item : [];
  }

  static function addItemToCart(PDO $db, int $id, int $item_id, ?float $price): ?array
  {
    $shipping = round($price * 0.05, 2); // TODO Create function to calculate the shipping cost;
    try {
      $stmt = $db->prepare('
          SELECT * FROM user_cart
          WHERE item = ? AND user = ?
      ');

      $stmt->execute([$item_id, $id]);

      $item = $stmt->fetchAll();

      if (empty($item)) {
        $stmt = $db->prepare('
          INSERT INTO user_cart (item, user, price, shipping)
          VALUES (?, ?, ?, ?)
        ');
        $stmt->execute([$item_id, $id, $price, $shipping]);
      } else {
        $stmt = $db->prepare('
          UPDATE user_cart
          SET price = ?
          WHERE item = ? AND user = ?
        ');
        $stmt->execute([$price, $item_id, $id]);
      }

      return User::getCartItem($db, $id, $item_id);

    } catch (PDOException $e) {
      throw $e;
    }
  }

  static function removeItemFromCart(PDO $db, int $user_id, int $item_id)
  {
    try {
      $stmt = $db->prepare('
            DELETE FROM user_cart
            WHERE item = ? AND user = ?
        ');
      $stmt->execute([$item_id, $user_id]);
    } catch (PDOException $e) {
      throw $e; // Re-throwing the exception to be caught in the calling code
    }
  }

  static function purchaseCart(PDO $db, int $id)
  {
    try {
      $cart = User::getCart($db, $id);

      foreach ($cart as $cartItem) {
        Item::buyItem($db, $id, $cartItem['item_id'], $cartItem['new_price']);
      }
    } catch (PDOException $e) {
      throw $e;
    }
  }

  static function getBoughtItems(PDO $db, int $id): array
  {
    $stmt = $db->prepare('
            SELECT item.id
            FROM item
            WHERE item.buyer = ?
          ');
    $stmt->execute([$id]);

    $items_id = $stmt->fetchAll() ?: [];

    $boughtItems = [];

    foreach ($items_id as $item_id) {
      $item = Item::getItem($db, $item_id['id']);
      $seller = User::getUser($db, $item->seller);
      $seller->password = null;
      $boughtItem = array(
        'item' => $item,
        'seller' => $seller,
      );
      $boughtItems[] = $boughtItem;
    }

    return $boughtItems;
  }

  static function isEmailRegistered(PDO $db, string $email): bool
  {
    $stmt = $db->prepare('
              SELECT COUNT(*) AS count
              FROM user
              WHERE lower(email) = ?
          ');

    $stmt->execute([strtolower($email)]);
    $result = $stmt->fetch();

    return $result['count'] > 0;
  }

  static function changeUserPassword(PDO $db, int $id, string $newPassword): ?User
  {
    $stmt = $db->prepare('
      UPDATE user
      SET password = ?
      WHERE id = ?
    ');
    $stmt->execute([
      password_hash($newPassword, PASSWORD_DEFAULT, ['cost' => 12]),
      $id,
    ]);

    return User::getUser($db, (int) $id);
  }
}
?>