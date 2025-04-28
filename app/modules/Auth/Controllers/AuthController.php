<?php
namespace App\Modules\Auth\Controllers;

use Core\Controller;
use App\Modules\Auth\Models\User;
use Core\Auth;

class AuthController extends Controller
{
    public function home(): void
    {
        // ensure session + login
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!Auth::check()) {
            $this->redirect('/login');
        }

        // fetch fresh user for display
        $user = User::find(Auth::userId());

        echo "Welcome to the BIMS system! Logged in as: "
           . htmlspecialchars($user->email);
    }

    public function loginForm(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // generate CSRF if missing
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        // render your Tailwind/Alpine login view
        $this->view('Auth/Views/login');
    }

    public function login(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Safely pull both tokens as strings (fall back to empty string)
    $sessionToken = $_SESSION['csrf_token'] ?? '';
    $postedToken  = $_POST['csrf_token']  ?? '';

    // If there's no valid token or they don't match, bail out
    if ('' === $sessionToken || !hash_equals($sessionToken, (string)$postedToken)) {
        $_SESSION['flash']['error'] = 'Invalid session. Please try again.';
        $this->redirect('/login');
    }

    $email    = $_POST['email']    ?? '';
    $password = $_POST['password'] ?? '';

    $user = User::where('email', $email)->first();
    if ($user && password_verify($password, $user->password)) {
        $_SESSION['user_id']         = $user->id;
        $_SESSION['flash']['success'] = 'Welcome back!';
        $this->redirect('/');
    }

    $_SESSION['flash']['error'] = 'Invalid credentials.';
    $this->redirect('/login');
}


    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_destroy();
        $this->redirect('/login');
    }
}
