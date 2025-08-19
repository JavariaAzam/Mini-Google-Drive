<?php
declare(strict_types=1);

/* Simple per-IP limiter in session (swap for Redis in prod) */
function throttle(string $key, int $max, int $windowSec): bool {
  $now = time();
  $_SESSION['throttle'][$key] = array_filter($_SESSION['throttle'][$key] ?? [], fn($t)=>$t > $now-$windowSec);
  $hits = count($_SESSION['throttle'][$key]);
  if ($hits >= $max) return false;
  $_SESSION['throttle'][$key][] = $now;
  return true;
}
