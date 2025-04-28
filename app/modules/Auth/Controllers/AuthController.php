<?php
namespace App\Modules\Auth\Controllers;

use Core\Controller;

/**
 * Class AuthController
 *
 * @package App\Modules\Auth\Controllers
 */
class AuthController extends Controller
{
public function loginForm(): void { /* show login view */ }
public function login(): void { /* validate credentials, start session */ }
public function registerForm(): void { /* show register view */ }
public function register(): void { /* create user */ }
public function logout(): void { /* destroy session */ }
}
