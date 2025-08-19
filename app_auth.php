<?php
declare(strict_types=1);

function require_login(): void {
  if (empty($_SESSION['user'])) {
    header('Location: /login.php'); exit;
  }
}
function current_user(): ?array {
  return $_SESSION['user'] ?? null;
}
function require_admin(): void {
  if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403); exit('Forbidden');
  }
}
