<?php
declare(strict_types=1);

class User
{
  public int $id;
  public string $firstName;
  public string $lastName;
  public string $address;
  public string $city;
  public string $state;
  public string $country;
  public string $postalcode;
  public string $email;
  public string $registrationDate;

  public function __construct(int $id, string $firstName, string $lastName, string $address, string $city, string $state, string $country, string $postalcode, string $email, string $registrationDate)
  {
    $this->id = $id;
    $this->firstName = $firstName;
    $this->lastName = $lastName;
    $this->address = $address;
    $this->city = $city;
    $this->state = $state;
    $this->country = $country;
    $this->postalcode = $postalcode;
    $this->email = $email;
    $this->registrationDate = $registrationDate;
  }

  function name()
  {
    return $this->firstName . ' ' . $this->lastName;
  }

  function save($db)
  {
    $stmt = $db->prepare('
        UPDATE user SET first_name = ?, last_name = ?
        WHERE id = ?
      ');

    $stmt->execute(array($this->firstName, $this->lastName, $this->id));
  }

  static function getUserWithPassword(PDO $db, string $email, string $password): ?User
  {
    $stmt = $db->prepare('
        SELECT id, first_name, last_name, address, city, state, country, postal_code, email, registration_date
        FROM user 
        WHERE lower(email) = ? AND password = ?
      ');

    $stmt->execute(array(strtolower($email), sha1($password))); //change to sha1($password);

    if ($user = $stmt->fetch()) {
      return new User(
        $user['id'],
        $user['first_name'],
        $user['last_name'],
        $user['address'],
        $user['city'],
        $user['state'],
        $user['country'],
        $user['postal_code'],
        $user['email'],
        $user['registration_date']
      );
    } else
      return null;
  }

  static function getUser(PDO $db, int $id): User
  {
    $stmt = $db->prepare('
        SELECT id, first_name, last_name, address, city, state, country, postal_code, email, registration_date
        FROM user 
        WHERE id = ?
      ');

    $stmt->execute(array($id));
    $user = $stmt->fetch();

    return new User(
      $user['id'],
      $user['first_name'],
      $user['last_name'],
      $user['address'],
      $user['city'],
      $user['state'],
      $user['country'],
      $user['postal_code'],
      $user['email'],
      $user['registration_date']
    );
  }

}
?>