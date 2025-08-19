<?php
require __DIR__ . '/../app_config.php';
require __DIR__ . '/../app_userRepo.php';
require __DIR__ . '/../app_CSRF.php';
require __DIR__ . '/../app_rateLimit.php';

if (!empty($_SESSION['user'])) { header('Location: /files.php'); exit; }

$msg = isset($_GET['registered']) ? 'Registration successful. Please log in.' : '';
$err = '';

if ($_SERVER['REQUEST_METHOD']==='POST') {
  if (!throttle('login_'.($_SERVER['REMOTE_ADDR']??'ip'), 10, 60)) {
    $err = 'Too many attempts. Try again in a minute.';
  } elseif (!csrf_verify($_POST['csrf'] ?? '')) {
    $err = 'Invalid CSRF token.';
  } else {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $u = find_user_by_email($pdo,$email);
    if (!$u || !password_verify($pass, $u['password'])) {
      $err = 'Invalid credentials.';
    } else {
      session_regenerate_id(true);
      $_SESSION['user'] = ['id'=>$u['id'],'name'=>$u['name'],'email'=>$u['email'],'role'=>$u['role']];
      header('Location: /files.php'); exit;
    }
  }
}

?>
<!doctype html><html><head>
<meta charset="utf-8"><title>Login</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head><body class="container py-5">
<h3>Login</h3>
<?php if($msg): ?><div class="alert alert-success"><?=$msg?></div><?php endif; ?>
<?php if($err): ?><div class="alert alert-danger"><?=$err?></div><?php endif; ?>
<form method="post" class="vstack gap-3" autocomplete="off">
  <input class="form-control" name="email" placeholder="Email" type="email" required>
  <input class="form-control" name="password" placeholder="Password" type="password" required>
  <button class="btn btn-primary">Login</button>
  <a href="/register.php">Create account</a>
</form>
<form method="post" class="vstack gap-3" autocomplete="off">
  <?= csrf_field() ?>
  ...
</form>

</body></html>
