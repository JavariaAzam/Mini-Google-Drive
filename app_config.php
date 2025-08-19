<?php
declare(strict_types=1);

session_start();

// --- DB CONFIG ---
const DB_HOST = '127.0.0.1';
const DB_NAME = 'sql_schema';
const DB_USER = 'root';
const DB_PASS = 'Javairia123';

// --- UPLOAD CONFIG ---
const STORAGE_DIR = __DIR__ . '/../storage';
const MAX_BYTES    = 10 * 1024 * 1024;                     // 10 MB
const ALLOWED_MIME = ['application/pdf','image/png','image/jpeg','application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

// PDO (strict, exceptions, emulation off)
$pdo = new PDO(
  "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4",
  DB_USER, DB_PASS,
  [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
  ]
);

// ensure storage
if (!is_dir(STORAGE_DIR)) { mkdir(STORAGE_DIR, 0770, true); }
