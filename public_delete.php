<?php
require __DIR__ . '/../app_config.php';
require __DIR__ . '/../app_auth.php';
require __DIR__ . '/../app_fileRepo.php';
require_login();
$user = current_user();

$id = (int)($_GET['id'] ?? 0);
$rec = get_file_for_user($pdo, $id, (int)$user['id'], $user['role']==='admin');
if ($rec) {
  $path = STORAGE_DIR . '/' . $rec['stored_name'];
  if (delete_file($pdo, $id, (int)$user['id'], $user['role']==='admin')) {
    if (is_file($path)) @unlink($path);
  }
}
header('Location: /files.php');
