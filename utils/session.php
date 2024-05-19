<?php
class Session
{
  private array $messages;

  public function __construct()
  {
    session_start();

    $this->messages = isset($_SESSION['messages']) ? $_SESSION['messages'] : array();
    unset($_SESSION['messages']);
  }

  public function isLoggedIn(): bool
  {
    return isset($_SESSION['id']);
  }

  public function logout()
  {
    session_destroy();
  }

  public function getId(): ?int
  {
    return isset($_SESSION['id']) ? $_SESSION['id'] : null;
  }

  public function getName(): ?string
  {
    return isset($_SESSION['name']) ? $_SESSION['name'] : null;
  }

  public function setId(int $id)
  {
    $_SESSION['id'] = $id;
  }

  public function setName(string $name)
  {
    $_SESSION['name'] = $name;
  }

  public function generateSessionToken()
  {
    $_SESSION['csrf'] = bin2hex(openssl_random_pseudo_bytes(32));
  }

  public function getSessionToken(): ?string
  {
    return isset($_SESSION['csrf']) ? $_SESSION['csrf'] : null;
  }

}
?>