<?php
namespace App\Modules\Auth\Controllers;

use Core\Controller;
use App\Modules\Auth\Models\User;
use Core\Auth;
use Core\Settings; // Import the Settings class

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
    
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
    
        $lockoutTimes = Settings::get('login.lockout_times', []);
        $maxAttempts  = Settings::get('login.max_attempts', 5);
    
        $attempts = $_SESSION['login_attempts'][$email]['count'] ?? 0;
        $lastAttempt = $_SESSION['login_attempts'][$email]['time'] ?? 0;
    
        // Lockout check first
        if (isset($lockoutTimes[$attempts])) {
            if ($lockoutTimes[$attempts] === "admin") {
                $_SESSION['flash']['error'] = "Account locked. Admin unlock required.";
                $this->redirect('/login');
            }
    
            $lockoutDuration = (int) $lockoutTimes[$attempts];
            $timeRemaining = $lockoutDuration - (time() - $lastAttempt);
    
            if ($timeRemaining > 0) {
                $minutes = ceil($timeRemaining / 60);
                $_SESSION['flash']['error'] = "Account temporarily locked. Try again in {$minutes} minute(s).";
                $this->redirect('/login');
            }
        }
    
        // Try to find user
        $user = User::where('email', $email)->first();
    
        if ($user && password_verify($password, $user->password)) {
            unset($_SESSION['login_attempts'][$email]); // Clear attempts on success
            $_SESSION['user_id'] = $user->id;
            $_SESSION['flash']['success'] = 'Welcome back!';
            $this->redirect('/');
        }
    
        // Failed login handling
        $attempts++;
        $_SESSION['login_attempts'][$email]['count'] = $attempts;
        $_SESSION['login_attempts'][$email]['time'] = time();
    
        // Calculate attempts remaining
        $attemptsLeft = $maxAttempts - $attempts;
        if ($attemptsLeft > 0) {
            $_SESSION['flash']['error'] = "Invalid credentials. {$attemptsLeft} attempt(s) remaining before lockout.";
        } else {
            $_SESSION['flash']['error'] = "Too many failed attempts. Account locked.";
        }
    
        $this->redirect('/login');
    }
    


    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_destroy();
        $this->redirect('/login');
    }
}
