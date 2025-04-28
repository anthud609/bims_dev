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
    
        $email    = $_POST['email']    ?? '';
        $password = $_POST['password'] ?? '';
    
        // Hard-coded max attempts
        $maxAttempts = 3;
    
        // Find the user
        $user = User::where('email', $email)->first();
    
        // If no such user, sleep and redirect
        if (! $user) {
            sleep(1);
            $_SESSION['flash']['error'] = "Invalid credentials.";
            $this->redirect('/login');
        }
    
        // If already admin-locked or hit maxFailures
        if ($user->is_locked || $user->failed_attempts >= $maxAttempts) {
            $_SESSION['flash']['error'] = "Account locked after {$maxAttempts} failed attempts.";
            $this->redirect('/login');
        }
    
        // OK to try password
        if (password_verify($password, $user->password)) {
            // ✅ success: reset counters
            $user->failed_attempts = 0;
            $user->last_failed_at  = null;
            $user->is_locked       = 0;
            $user->save();
    
            $_SESSION['user_id']          = $user->id;
            $_SESSION['flash']['success'] = "Welcome back!";
            $this->redirect('/');
        }
    
        // ❌ failure: bump counter
        $user->failed_attempts += 1;
        $user->last_failed_at  = date('Y-m-d H:i:s');
    
        if ($user->failed_attempts >= $maxAttempts) {
            // final lock
            $user->is_locked = 1;
            $_SESSION['flash']['error'] = "Too many failed attempts. Account locked.";
        } else {
            $left = $maxAttempts - $user->failed_attempts;
            $_SESSION['flash']['error'] = "Invalid credentials. {$left} attempt" 
                                        . ($left === 1 ? '' : 's') 
                                        . " remaining.";
        }
    
        $user->save();
        $this->redirect('/login');
    }
    
    
    
    


    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_destroy();
        $this->redirect('/login');
    }
}
