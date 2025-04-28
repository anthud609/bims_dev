
<div class="flex items-center justify-center h-[calc(100vh-4rem)]">
  <!-- Toast container -->
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

  <!-- Login Card -->
  <div class="w-full max-w-md bg-white rounded-lg shadow-md p-8">
    <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Sign In</h2>
    <form method="POST" action="/login" class="space-y-4">
      <!-- CSRF hidden field -->
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

      <div>
        <label class="block text-gray-700 mb-1" for="email">Email</label>
        <input
          id="email" name="email" type="email" required
          class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-400"
        >
      </div>

      <div>
        <label class="block text-gray-700 mb-1" for="password">Password</label>
        <input
          id="password" name="password" type="password" required
          class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-400"
        >
      </div>
      <?php if (!empty($showCaptcha)): ?>
  <!-- Google reCAPTCHA v3 (or v2 checkbox) -->
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
  <div class="g-recaptcha" data-sitekey="<?= $_ENV['RECAPTCHA_SITE_KEY'] ?>"></div>
<?php endif; ?>

      <button
        type="submit"
        class="w-full py-2 bg-purple-600 text-white rounded hover:bg-purple-700 transition"
      >
        Log In
      </button>
    </form>
  </div>

  <script>
    function sessionFlash() {
      return {
        toast: { show: false, message: '', type: 'success' },
        init() {
          // Pull flash from server-side session into JS
          <?php if (!empty($_SESSION['flash'])): ?>
            <?php foreach ($_SESSION['flash'] as $type => $msg): ?>
              this.toast = { show: true, message: <?= json_encode($msg) ?>, type: <?= json_encode($type) ?> };
            <?php endforeach; unset($_SESSION['flash']); ?>
          <?php endif; ?>
          // auto-hide after 3s
          if (this.toast.show) {
            setTimeout(() => this.toast.show = false, 3000);
          }
        }
      }
    }
  </script>
</div>

