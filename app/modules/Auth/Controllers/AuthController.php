<?php
namespace App\Modules\Auth\Controllers;

use Core\Controller;
use App\Modules\Auth\Models\User;
use Core\Auth;
use Core\Settings; // Import the Settings class
use Carbon\Carbon;
use App\Modules\Auth\Models\Session as UserSession;
use Illuminate\Support\Str;
use Core\LoggingServiceProvider;

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
        // fetch the PSR-3 logger
        $logger = LoggingServiceProvider::getLogger();
    
        $logger->debug('login() invoked', [
            'session_status' => session_status(),
        ]);
    
        if (session_status()===PHP_SESSION_NONE) {
            session_start();
            $logger->debug('PHP session started');
        }
    
        // check already-auth’d
        if (Auth::check()) {
            $logger->info('User already authenticated, redirecting to /');
            $this->redirect('/');
        }
    
        // grab inputs
        $email    = $_POST['email']    ?? '';
        $password = $_POST['password'] ?? '';
        $logger->debug('Credentials received', ['email' => $email]);
    
        $maxAttempts = 3;
        $_SESSION['last_email'] = $email;
        $logger->debug('Saved last_email to session', ['last_email' => $_SESSION['last_email']]);
    
        // CAPTCHA step
        if (! empty($_POST['g-recaptcha-response'])) {
            $logger->debug('Verifying reCAPTCHA');
            $resp   = $_POST['g-recaptcha-response'];
            $verify = file_get_contents(
              "https://www.google.com/recaptcha/api/siteverify?"
             . "secret={$_ENV['RECAPTCHA_SECRET_KEY']}&response={$resp}"
            );
            $json = json_decode($verify, true);
            $logger->debug('reCAPTCHA response', $json);
    
            if (empty($json['success']) || ($json['score'] ?? 0) < 0.5) {
                $logger->warning('reCAPTCHA failed, user might be bot');
                $_SESSION['flash']['error'] = "CAPTCHA failed—prove you’re human.";
                $this->redirect('/login');
            }
        }
    
        // lookup user
        $logger->debug('Querying user by email', ['email' => $email]);
        $user = User::where('email', $email)->first();
    
        if (! $user) {
            $logger->warning('Login failed: user not found', ['email' => $email]);
            sleep(1);
            $_SESSION['flash']['error'] = "Invalid credentials.";
            $this->redirect('/login');
        }
    
        // locked-out check
        if ($user->is_locked || $user->failed_attempts >= $maxAttempts) {
            $logger->warning('Login blocked: account locked', [
                'user_id'        => $user->id,
                'failed_attempts'=> $user->failed_attempts,
            ]);
            $_SESSION['flash']['error'] = "Account locked after {$maxAttempts} failed attempts.";
            $this->redirect('/login');
        }
    
        // password verify
        if (password_verify($password, $user->password)) {
            $logger->info('Password verified, logging in user', ['user_id' => $user->id]);
    
            // reset counters
            $user->failed_attempts = 0;
            $user->last_failed_at  = null;
            $user->is_locked       = 0;
            $user->save();
            $logger->debug('User failed_attempts reset to zero');
    
            // create session row
            $token   = bin2hex(random_bytes(32));
            $expires = Carbon::now()->addDays(14);
            $session = UserSession::create([
                'id'            => Str::uuid()->toString(),
                'user_id'       => $user->id,
                'token'         => $token,
                'ip_address'    => $_SERVER['REMOTE_ADDR'] ?? null,
                'user_agent'    => $_SERVER['HTTP_USER_AGENT'] ?? null,
                'expires_at'    => $expires,
                'last_activity' => Carbon::now(),
            ]);
            $logger->debug('New session record created', [
                'session_id' => $session->id,
                'expires_at' => $expires->toDateTimeString(),
            ]);
    
            // store in PHP session
            $_SESSION['session_token'] = $token;
            $logger->debug('Session token stored in PHP session');
    
            $_SESSION['flash']['success'] = "Welcome back!";
            $logger->info('Redirecting to dashboard after successful login', [
                'user_id' => $user->id,
            ]);
            $this->redirect('/');
        }
    
        // failure path
        $user->failed_attempts += 1;
        $user->last_failed_at  = date('Y-m-d H:i:s');
        $user->save();
        $logger->warning('Password verify failed', [
            'user_id'        => $user->id,
            'failed_attempts'=> $user->failed_attempts,
        ]);
    
        if ($user->failed_attempts >= $maxAttempts) {
            $_SESSION['flash']['error'] = "Too many failed attempts. Account locked.";
            $logger->alert('Account locked due to too many failures', [
                'user_id' => $user->id,
            ]);
        } else {
            $left = $maxAttempts - $user->failed_attempts;
            $_SESSION['flash']['error'] = "Invalid credentials. {$left} attempt" 
                                        . ($left === 1 ? '' : 's') 
                                        . " remaining.";
            $logger->notice('Login attempt remaining', [
                'user_id'         => $user->id,
                'attempts_left'   => $left,
            ]);
        }
    
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
