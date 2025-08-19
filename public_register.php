<?php
require __DIR__ . '/../app_config.php';
require __DIR__ . '/../app_userRepo.php';
require __DIR__ . '/../app_CSRF.php';

$err = '';
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $name = trim($_POST['name'] ?? '');
  $email= trim($_POST['email'] ?? '');
  $pass = $_POST['password'] ?? '';
  $cpass= $_POST['confirm'] ?? '';

  if ($name==='' || $email==='' || $pass==='' || $cpass==='') {
    $err = 'All fields are required.';
  } elseif (!filter_var($email,FILTER_VALIDATE_EMAIL)) {
    $err = 'Invalid email.';
  } elseif ($pass !== $cpass) {
    $err = 'Passwords do not match.';
  } 
  elseif (find_user_by_email($pdo,$email)) {
    $err = 'Email already registered.';
  } 
   elseif (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,72}$/', $pass)) {
  $err = 'Password must be 8+ chars with upper, lower, number.';
}

  else {
    create_user($pdo,$name,$email,$pass);
    header('Location: /login.php?registered=1'); exit;
  }
}
?>
<!doctype html><html><head>
<meta charset="utf-8"><title>Register</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head><body class="container py-5">
<h3>Create Account</h3>
<?php if($err): ?><div class="alert alert-danger"><?=htmlspecialchars($err)?></div><?php endif; ?>
<form method="post" class="vstack gap-3" autocomplete="off">
  <input class="form-control" name="name" placeholder="Full name" required>
  <input class="form-control" name="email" placeholder="Email" type="email" required>
  <input class="form-control" name="password" placeholder="Password" type="password" required>
  <input class="form-control" name="confirm" placeholder="Confirm password" type="password" required>
  <button class="btn btn-primary">Register</button>
  <a href="/login.php">Already have an account?</a>
  <?= csrf_field() ?>
</form>
</body></html>
