<?php
namespace App\Modules\Auth\Controllers;

use Core\Controller;
use App\Modules\Auth\Models;

/**
 * Class AuthController
 *
 * @package App\Modules\Auth\Controllers
 */
class AuthController extends Controller
{
    
    public function home(): void
{
    echo "Welcome to the BIMS system!";
}
public function loginForm(): void
    {
        include __DIR__ . '/../Views/login.php';
    }
    public function login(): void
{
    session_start();
    
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;
    
    $user = User::where('email', $email)->first();

    if ($user && password_verify($password, $user->password)) {
        $_SESSION['user_id'] = $user->id;
        header('Location: /dashboard');
        exit;
    }
    
    echo "Login failed.";
}

public function registerForm(): void { /* show register view */ }
public function register(): void { /* create user */ }
public function logout(): void { /* destroy session */ }
}
