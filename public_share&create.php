<?php
require __DIR__ . '/../app_config.php';
require __DIR__ . '/../app_auth.php';
require __DIR__ . '/../app_fileRepo.php';
require __DIR__ . '/../app_CSRF.php';
require_login();
$user = current_user();

if ($_SERVER['REQUEST_METHOD']!=='POST' || !csrf_verify($_POST['csrf'] ?? '')) {
  http_response_code(400); exit('Bad request');
}
$id = (int)($_POST['id'] ?? 0);
$hours = max(1, min(168, (int)($_POST['hours'] ?? 24))); // 1h..7d
$rec = get_file_for_user($pdo, $id, (int)$user['id'], $user['role']==='admin');
if (!$rec) { http_response_code(404); exit('Not found'); }

$token = bin2hex(random_bytes(32));
$expires = (new DateTime("+{$hours} hours"))->format('Y-m-d H:i:s');
$stmt = $pdo->prepare("INSERT INTO file_shares(file_id, token, expires_at) VALUES(?,?,?)");
$stmt->execute([$rec['id'], $token, $expires]);

$shareUrl = sprintf('%s://%s/share_download.php?t=%s',
  (!empty($_SERVER['HTTPS'])?'https':'http'),
  $_SERVER['HTTP_HOST'],
  urlencode($token)
);
echo htmlspecialchars($shareUrl);
