<?php
require __DIR__ . '/../app_config.php';

$token = $_GET['t'] ?? '';
if (!preg_match('/^[a-f0-9]{64}$/', $token)) { http_response_code(400); exit('Bad token'); }

$stmt = $pdo->prepare("SELECT fs.expires_at, f.filename, f.stored_name, f.mime
                       FROM file_shares fs
                       JOIN files f ON f.id = fs.file_id
                       WHERE fs.token=? LIMIT 1");
$stmt->execute([$token]);
$row = $stmt->fetch();

if (!$row) { http_response_code(404); exit('Not found'); }
if (new DateTime() > new DateTime($row['expires_at'])) { http_response_code(410); exit('Link expired'); }

$path = STORAGE_DIR . '/' . $row['stored_name'];
if (!is_file($path)) { http_response_code(410); exit('Gone'); }

header('Content-Type: '.$row['mime']);
header('Content-Disposition: attachment; filename="'.basename($row['filename']).'"');
header('Content-Length: '.filesize($path));
readfile($path);
