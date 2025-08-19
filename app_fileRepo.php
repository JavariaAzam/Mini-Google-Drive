<?php
declare(strict_types=1);

function list_files_for_user(PDO $pdo, int $user_id): array {
  $stmt = $pdo->prepare("SELECT * FROM files WHERE user_id=? ORDER BY uploaded_at DESC");
  $stmt->execute([$user_id]);
  return $stmt->fetchAll();
}

function get_file_for_user(PDO $pdo, int $id, int $user_id, bool $isAdmin=false): ?array {
  if ($isAdmin) {
    $stmt = $pdo->prepare("SELECT * FROM files WHERE id=?");
    $stmt->execute([$id]);
  } else {
    $stmt = $pdo->prepare("SELECT * FROM files WHERE id=? AND user_id=?");
    $stmt->execute([$id, $user_id]);
  }
  $f = $stmt->fetch();
  return $f ?: null;
}

function insert_file(PDO $pdo, int $user_id, string $orig, string $stored, int $size, string $mime): int {
  $stmt = $pdo->prepare("INSERT INTO files(user_id,filename,stored_name,size,mime) VALUES(?,?,?,?,?)");
  $stmt->execute([$user_id,$orig,$stored,$size,$mime]);
  return (int)$pdo->lastInsertId();
}

function delete_file(PDO $pdo, int $id, int $user_id, bool $isAdmin=false): bool {
  if ($isAdmin) {
    $stmt = $pdo->prepare("DELETE FROM files WHERE id=?");
    return $stmt->execute([$id]);
  } else {
    $stmt = $pdo->prepare("DELETE FROM files WHERE id=? AND user_id=?");
    return $stmt->execute([$id,$user_id]);
  }
}
