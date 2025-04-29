<?php
// app/Views/errors/500.dev.php

/** @var \Throwable $exception */
http_response_code(500);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>🛑 Server Error</title>
  <style>
    body { font-family: Menlo, monospace; background: #f2f2f2; color: #333; padding: 2em; }
    h1 { color: #c00; }
    pre { background: #fff; padding: 1em; border: 1px solid #ccc; overflow: auto; }
    .info { margin: 1em 0; }
    .label { font-weight: bold; }
  </style>
</head>
<body>
  <h1>500 — Internal Server Error</h1>
  <div class="info">
    <span class="label">Message:</span>
    <pre><?= htmlspecialchars($exception->getMessage()) ?></pre>
  </div>
  <div class="info">
    <span class="label">In:</span>
    <pre><?= htmlspecialchars($exception->getFile() . ' on line ' . $exception->getLine()) ?></pre>
  </div>
  <div class="info">
    <span class="label">Stack trace:</span>
    <pre><?= htmlspecialchars($exception->getTraceAsString()) ?></pre>
  </div>
</body>
</html>
