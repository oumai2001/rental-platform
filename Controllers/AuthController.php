<?php

namespace Controllers;

use Services\AuthService;

require_once __DIR__ . '/../config/app.php';

class AuthController
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function showLogin(): void
    {
        require __DIR__ . '/../views/auth/login.php';
    }

    public function handleLogin(): void
    {
        try {
            $user = $this->authService->login(
                $_POST['email'] ?? '',
                $_POST['password'] ?? ''
            );

            $_SESSION['success'] = "Bienvenue " . $user->getFullName();
            header('Location: ' . BASE_PATH . '/dashboard');
            exit;

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: ' . BASE_PATH . '/login');
            exit;
        }
    }

    public function showRegister(): void
    {
        require __DIR__ . '/../views/auth/register.php';
    }

    public function handleRegister(): void
    {
        try {
            $this->authService->register($_POST);
            $_SESSION['success'] = "Inscription réussie";
            header('Location: ' . BASE_PATH . '/login');
            exit;

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: ' . BASE_PATH . '/register');
            exit;
        }
    }

    public function logout(): void
    {
        $this->authService->logout();
    }
}
