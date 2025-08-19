<?php
require __DIR__ . '/../app_config.php';
require __DIR__ . '/../app_auth.php';
require __DIR__ . '/../app_fileRepo.php';
require_login();
$user = current_user();

$id = (int)($_GET['id'] ?? 0);
$record = get_file_for_user($pdo, $id, (int)$user['id'], $user['role']==='admin');
if (!$record) { http_response_code(404); exit('Not found'); }

$path = STORAGE_DIR . '/' . $record['stored_name'];
if (!is_file($path)) { http_response_code(410); exit('Gone'); }

header('Content-Description: File Transfer');
header('Content-Type: '.$record['mime']);
header('Content-Disposition: attachment; filename="'.basename($record['filename']).'"');
header('Content-Length: '.filesize($path));
readfile($path);
