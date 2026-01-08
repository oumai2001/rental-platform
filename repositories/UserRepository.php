<?php
namespace Repositories;

use Config\Database;
use Models\User;
use Exceptions\NotFoundException;
use PDO;

class UserRepository {
    private PDO $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function create(User $user): User {
        $sql = "INSERT INTO users (email, password, first_name, last_name, role, phone) 
                VALUES (:email, :password, :first_name, :last_name, :role, :phone)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':email' => $user->getEmail(),
            ':password' => $user->getPassword(),
            ':first_name' => $user->getFirstName(),
            ':last_name' => $user->getLastName(),
            ':role' => $user->getRole(),
            ':phone' => $user->getPhone()
        ]);
        
        $user->setId((int)$this->db->lastInsertId());
        return $user;
    }
    
    public function findById(int $id): User {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch();
        
        if (!$data) {
            throw new NotFoundException("User not found");
        }
        
        return new User($data);
    }
    
    public function findByEmail(string $email): ?User {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $data = $stmt->fetch();
        
        return $data ? new User($data) : null;
    }
    
    public function findAll(): array {
        $stmt = $this->db->query("SELECT * FROM users ORDER BY created_at DESC");
        $users = [];
        while ($data = $stmt->fetch()) {
            $users[] = new User($data);
        }
        return $users;
    }
    
    public function update(User $user): bool {
        $sql = "UPDATE users SET first_name = :first_name, last_name = :last_name, 
                phone = :phone WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $user->getId(),
            ':first_name' => $user->getFirstName(),
            ':last_name' => $user->getLastName(),
            ':phone' => $user->getPhone()
        ]);
    }
    
    public function toggleActive(int $userId): bool {
        $sql = "UPDATE users SET is_active = NOT is_active WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $userId]);
    }
    
    public function count(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM users")->fetchColumn();
    }
    
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}