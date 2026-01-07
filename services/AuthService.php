<?php

namespace Services;

use Repositories\UserRepository;
use Models\User;

class AuthService
{
    private UserRepository $userRepo;

    public function __construct()
    {
        $this->userRepo = new UserRepository();
    }

    /* ===================== REGISTER ===================== */
    public function register(array $data): User
    {
        if (empty($data['email']) || empty($data['password'])) {
            throw new \Exception("Email et mot de passe requis");
        }

        if ($this->userRepo->findByEmail($data['email'])) {
            throw new \Exception("Email déjà utilisé");
        }

        $user = new User();
        $user->setEmail($data['email']);
        $user->setPassword($data['password']); // hashing dans le model
        $user->setFirstName($data['first_name'] ?? '');
        $user->setLastName($data['last_name'] ?? '');
        $user->setRole($data['role'] ?? 'voyageur');
        $user->setPhone($data['phone'] ?? null);

        return $this->userRepo->create($user);
    }

    /* ===================== LOGIN ===================== */
    public function login(string $email, string $password): User
    {
        $user = $this->userRepo->findByEmail($email);

        if (!$user || !$user->verifyPassword($password)) {
            throw new \Exception("Identifiants incorrects");
        }

        $_SESSION['user_id']   = $user->getId();
        $_SESSION['user_role'] = $user->getRole();

        return $user;
    }

    /* ===================== LOGOUT ===================== */
    public function logout(): void
    {
        session_destroy();
        header('Location: ' . BASE_PATH . '/login');
        exit;
    }

    /* ===================== CURRENT USER ===================== */
    public function getCurrentUser(): ?User
    {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        return $this->userRepo->findById($_SESSION['user_id']);
    }

    /* ===================== AUTH GUARD ===================== */
    public function requireAuth(): User
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/login');
            exit;
        }

        $user = $this->getCurrentUser();

        if (!$user) {
            session_destroy();
            header('Location: ' . BASE_PATH . '/login');
            exit;
        }

        return $user;
    }

    /* ===================== ROLE GUARD ===================== */
    public function requireRole(string $role): User
    {
        $user = $this->requireAuth();

        if (!$user->isAdmin() && $user->getRole() !== $role) {
            header('Location: ' . BASE_PATH . '/dashboard');
            exit;
        }

        return $user;
    }
}
