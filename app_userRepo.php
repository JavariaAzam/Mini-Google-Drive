<?php
declare(strict_types=1);

/** Create user (hashed password) */
function create_user(PDO $pdo, string $name, string $email, string $password): bool {
  $hash = password_hash($password, PASSWORD_BCRYPT);
  $stmt = $pdo->prepare("INSERT INTO users(name,email,password) VALUES(?,?,?)");
  return $stmt->execute([$name, strtolower(trim($email)), $hash]);
}

/** Find user by email */
function find_user_by_email(PDO $pdo, string $email): ?array {
  $stmt = $pdo->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
  $stmt->execute([strtolower(trim($email))]);
  $u = $stmt->fetch();
  return $u ?: null;
}
