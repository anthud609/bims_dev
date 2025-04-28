<?php
// app/Views/layout.php

use Core\Auth;
use App\Modules\Auth\Models\User;

// boot up flash/session for toast & grab current user
if (session_status() === PHP_SESSION_NONE) session_start();
$user = Auth::check()
      ? User::find(Auth::userId())
      : null;
?>
<!DOCTYPE html>
<html lang="en" x-data="sessionFlash()" x-init="init()">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= htmlspecialchars($title ?? 'BIMS') ?></title>
  <!-- Tailwind + Alpine.js -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
  <!-- HEADER WITH USER DROPDOWN -->
  <header class="bg-white shadow p-4 h-16 flex items-center justify-between">
    <a href="/" class="text-2xl font-bold text-purple-700">BIMS</a>
    <?php if ($user): ?>
    <div class="relative" x-data="{ open: false }">

      <div class="relative" x-data="{ open: false }">
  <button @click="open = !open" 
          class="flex items-center space-x-2 text-gray-800 hover:text-gray-600">
    <img src="https://via.placeholder.com/32" alt="Avatar" class="h-8 w-8 rounded-full">
    <span class="font-medium"><?= htmlspecialchars($user->email) ?></span>
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path d="M19 9l-7 7-7-7"/>
    </svg>
  </button>

  <!-- dropdown panel -->
  <div x-show="open" @click.away="open = false"
       x-transition
       class="absolute right-0 mt-2 w-64 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden z-50">
    
    <!-- top bar: company + sign out -->
    <div class="flex items-center justify-between px-4 py-2 border-b border-gray-200">
  <span class="text-sm font-semibold text-gray-700">StarTek, Inc.</span>
  <form method="POST" action="/logout" class="m-0">
    <!-- CSRF token if you’re enforcing one -->
    <input type="hidden" name="csrf_token"
           value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
    <button type="submit"
            class="text-sm text-gray-600 hover:underline">
      Sign out
    </button>
  </form>
</div>


    <!-- profile summary -->
    <div class="px-4 py-3 flex items-center space-x-3">
      <img src="https://via.placeholder.com/40" alt="Avatar" class="h-10 w-10 rounded-full">
      <div class="flex-1">
        <p class="font-medium text-gray-800">Jane Doe</p>
        <p class="text-sm text-gray-500">jane.doe@example.com</p>
        <a href="#"
           class="mt-1 inline-flex items-center text-sm text-purple-600 hover:underline">
          View account
          <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path d="M9 5l7 7-7 7"/>
          </svg>
        </a>
      </div>
    </div>

    <!-- status options -->
    <div class="border-t border-b border-gray-200">
      <ul class="divide-y divide-gray-200">
        <li>
          <a href="#"
             class="flex items-center justify-between px-4 py-3 hover:bg-gray-50">
            <div class="flex items-center space-x-2">
              <i class="fas fa-circle text-green-500 text-xs"></i>
              <span class="text-sm text-gray-700">Available</span>
            </div>
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path d="M9 5l7 7-7 7"/>
            </svg>
          </a>
        </li>
        <li>
          <a href="#"
             class="flex items-center justify-between px-4 py-3 hover:bg-gray-50">
            <div class="flex items-center space-x-2">
              <i class="fas fa-home text-gray-500"></i>
              <span class="text-sm text-gray-700">Working remotely</span>
            </div>
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path d="M9 5l7 7-7 7"/>
            </svg>
          </a>
        </li>
        <li>
          <a href="#"
             class="flex items-center justify-between px-4 py-3 hover:bg-gray-50">
            <div class="flex items-center space-x-2">
              <i class="fas fa-pencil-alt text-gray-500"></i>
              <span class="text-sm text-gray-700">Set status message</span>
            </div>
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path d="M9 5l7 7-7 7"/>
            </svg>
          </a>
        </li>
      </ul>
    </div>

    <!-- add another account -->
    <div class="px-4 py-3">
      <a href="#"
         class="flex items-center space-x-2 text-sm text-gray-700 hover:bg-gray-50 p-2 rounded-lg">
        <i class="fas fa-user-plus"></i>
        <span>Add another account</span>
      </a>
    </div>
  </div>
</div>

    </div>
    <?php endif; ?>
  </header>

  <!-- GLOBAL TOAST (same as on login page) -->
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

  <!-- PAGE CONTENT -->
  <main class="flex-1 p-6">
    <?php require $viewFile; ?>
  </main>

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
        if (this.toast.show) {
          setTimeout(() => this.toast.show = false, 3000);
        }
      }
    }
  }
  </script>
</body>
</html>
