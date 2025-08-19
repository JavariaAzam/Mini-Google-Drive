<?php
require __DIR__ . '/../app_config.php';
require __DIR__ . '/../app_auth.php';
require __DIR__ . '/../appfileRepo.php';
require_login();
$user = current_user();

$flash = '';
// before handling upload, compute current usage
$usageStmt = $pdo->prepare("SELECT COALESCE(SUM(size),0) AS used FROM files WHERE user_id=?");
$usageStmt->execute([(int)$user['id']]);
$used = (int)$usageStmt->fetch()['used'];

$quota = $pdo->prepare("SELECT storage_quota FROM users WHERE id=?");
$quota->execute([(int)$user['id']]);
$limit = (int)($quota->fetch()['storage_quota'] ?? 0); // 0 or NULL = unlimited when cast carefully

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_FILES['file'])) {
  if (!csrf_verify($_POST['csrf'] ?? '')) {
    $flash = 'Invalid CSRF token.';
  } elseif (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    $flash = 'Upload failed.';
  } else {
    $tmp  = $_FILES['file']['tmp_name'];
    $name = basename($_FILES['file']['name']);
    $size = (int)$_FILES['file']['size'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $tmp) ?: 'application/octet-stream';
    finfo_close($finfo);

    if ($size > MAX_BYTES) {
      $flash = 'File too large.';
    } elseif (!in_array($mime, ALLOWED_MIME, true)) {
      $flash = 'File type not allowed.';
    } elseif ($limit && ($used + $size) > $limit) {
      $flash = 'Storage quota exceeded.';
    } else {
      $stored = bin2hex(random_bytes(16)) . '_' . preg_replace('/[^a-zA-Z0-9._-]/','_', $name);
      $dest = STORAGE_DIR . '/' . $stored;
      if (move_uploaded_file($tmp, $dest)) {
        insert_file($pdo, (int)$user['id'], $name, $stored, $size, $mime);
        $flash = 'Upload successful.';
      } else { $flash = 'Failed to save file.'; }
    }
  }
}


$files = list_files_for_user($pdo,(int)$user['id']);
?>
<!doctype html><html><head>
<meta charset="utf-8"><title>Your Files</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head><body class="container py-4">
<div class="d-flex justify-content-between align-items-center">
  <h4>Hello, <?=htmlspecialchars($user['name'])?></h4>
  <a class="btn btn-outline-secondary" href="/logout.php">Logout</a>
</div>
<?php if($flash): ?><div class="alert alert-info my-3"><?=htmlspecialchars($flash)?></div><?php endif; ?>

<form method="post" enctype="multipart/form-data" class="my-3 d-flex gap-2">
  <input class="form-control" type="file" name="file" required>
  <button class="btn btn-primary">Upload</button>
</form>
<form method="post" enctype="multipart/form-data" class="my-3 d-flex gap-2">
  <?= csrf_field() ?>
  <input class="form-control" type="file" name="file" required>
  <button class="btn btn-primary">Upload</button>
</form>


<table class="table table-striped">
  <thead><tr><th>File</th><th>Size</th><th>Uploaded</th><th>Actions</th></tr></thead>
  <tbody>
  <?php foreach($files as $f): ?>
    <tr>
      <td><?=htmlspecialchars($f['filename'])?></td>
      <td><?=number_format($f['size']/1024,1)?> KB</td>
      <td><?=$f['uploaded_at']?></td>
      <td class="d-flex gap-2">
        <a class="btn btn-sm btn-success" href="/download.php?id=<?=$f['id']?>">Download</a>
        <a class="btn btn-sm btn-danger"  href="/delete.php?id=<?=$f['id']?>" onclick="return confirm('Delete file?')">Delete</a>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
</body></html>
