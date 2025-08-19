<?php
declare(strict_types=1);

function csrf_token(): string {
  if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
  return $_SESSION['csrf'];
}
function csrf_verify(string $token): bool {
  return hash_equals($_SESSION['csrf'] ?? '', $token);
}
function csrf_field(): string {
  return '<input type="hidden" name="csrf" value="'.htmlspecialchars(csrf_token()).'">';
}
