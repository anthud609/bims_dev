<?php
// app/Views/auth_layout.php
?>
<!DOCTYPE html>
<html lang="en" x-data="sessionFlash()" x-init="init()">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= htmlspecialchars($title ?? 'Login | BIMS') ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

  <!-- Toast (same as before) -->
  <template x-if="toast.show">
    <div 
      class="fixed top-5 right-5 bg-white border-l-4 p-4 rounded shadow-lg"
      :class="toast.type === 'success' ? 'border-green-500' : 'border-red-500'"
      x-text="toast.message"
      x-show="toast.show"
      x-transition
      @click="toast.show = false"
    ></div>
  </template>

  <!-- inject your login view RIGHT HERE -->
  <?php require $viewFile; ?>

  <script>
    function sessionFlash() {
      return {
        toast: { show: false, message: '', type: 'success' },
        init() {
          <?php if (!empty($_SESSION['flash'])): ?>
            <?php foreach ($_SESSION['flash'] as $type => $msg): ?>
              this.toast = { show: true, message: <?= json_encode($msg) ?>, type: <?= json_encode($type) ?> };
            <?php endforeach; unset($_SESSION['flash']); ?>
          <?php endif; ?>
          if (this.toast.show) setTimeout(() => this.toast.show = false, 3000);
        }
      }
    }
  </script>
</body>
</html>
