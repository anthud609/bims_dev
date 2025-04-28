<?php
namespace App\Modules\Auth\Controllers;

use Core\Controller;
use App\Modules\Auth\Models\User;
use Core\Auth;
use Core\Settings; // Import the Settings class
use Carbon\Carbon;
use App\Modules\Auth\Models\Session as UserSession;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function home(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (! Auth::check()) {
            $this->redirect('/login');
        }
    
        $user = User::find(Auth::userId());
    
        // send $user into the view instead of echoing here
        $this->view('Auth/Views/home', compact('user'));
    }
    
    public function loginForm(): void
    {
        if (session_status()===PHP_SESSION_NONE) session_start();
        // ← if they’re already authenticated, bounce them home
        if (Auth::check()) {
            $this->redirect('/');
        }

        $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));
        $showCaptcha = false;
        if (! empty($_SESSION['last_email'])) {
            $u = User::where('email', $_SESSION['last_email'])->first();
            $showCaptcha = $u && $u->failed_attempts >= 2;
        }

        // use your auth‐only layout
        $this->view('Auth/Views/login', compact('showCaptcha'), 'auth');
    }


    public function login(): void
    {
        if (session_status()===PHP_SESSION_NONE) session_start();
        // ← also check here in case someone POSTS /login by hand
        if (Auth::check()) {
            $this->redirect('/');
        }
    
        $email    = $_POST['email']    ?? '';
        $password = $_POST['password'] ?? '';
    
        // Hard-coded max attempts
        $maxAttempts = 3;
    
// preserve the email so loginForm() can inspect it
$_SESSION['last_email'] = $email;

// if we showed a captcha, verify it
if ($_POST['g-recaptcha-response'] ?? '' ) {
    $resp = $_POST['g-recaptcha-response'];
    $verify = file_get_contents(
      "https://www.google.com/recaptcha/api/siteverify?"
     . "secret={$_ENV['RECAPTCHA_SECRET_KEY']}&response={$resp}"
    );
    $json = json_decode($verify, true);
    if (empty($json['success']) || $json['score'] < 0.5) {
        $_SESSION['flash']['error'] = "CAPTCHA failed—prove you’re human.";
        $this->redirect('/login');
    }
}


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
            //  success: reset counters
            $user->failed_attempts = 0;
            $user->last_failed_at  = null;
            $user->is_locked       = 0;
            $user->save();
    
 // 1) Create a new session record
 $token = bin2hex(random_bytes(32));  // 64-char hex
 $expires = Carbon::now()->addDays(14); // e.g. 14-day TTL

 UserSession::create([
    'id'           => Str::uuid()->toString(),
    'user_id'      => $user->id,
    'token'        => $token,
    'ip_address'   => $_SERVER['REMOTE_ADDR'] ?? null,
    'user_agent'   => $_SERVER['HTTP_USER_AGENT'] ?? null,
    'expires_at'   => $expires,
    'last_activity'=> Carbon::now(),
]);

 // 2) Store the session token in PHP session or a secure cookie
 $_SESSION['session_token'] = $token;

 $_SESSION['flash']['success'] = "Welcome back!";
 $this->redirect('/');
}
    
        //  failure: bump counter
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
    
        if (! empty($_SESSION['session_token'])) {
            // Mark that session as revoked in the database
            \App\Modules\Auth\Models\Session::where('token', $_SESSION['session_token'])
                ->update([
                    'is_revoked'    => true,
                    'last_activity' => \Carbon\Carbon::now(),
                    // optionally: 'expires_at' => \Carbon\Carbon::now(),
                ]);
        }
    
        session_destroy();
        $this->redirect('/login');
    }
    
}
