<?php
namespace Models;

class User {
    private ?int $id = null;
    private string $email;
    private string $password;
    private string $firstName;
    private string $lastName;
    private string $role = 'voyageur';
    private ?string $phone = null;
    private bool $isActive = true;
    private ?\DateTime $createdAt = null;
    
    public function __construct(?array $data = null) {
        if ($data) {
            $this->id = $data['id'] ?? null;
            $this->email = $data['email'];
            $this->password = $data['password'];
            $this->firstName = $data['first_name'];
            $this->lastName = $data['last_name'];
            $this->role = $data['role'] ?? 'voyageur';
            $this->phone = $data['phone'] ?? null;
            $this->isActive = (bool)($data['is_active'] ?? true);
            $this->createdAt = isset($data['created_at']) ? new \DateTime($data['created_at']) : null;
        }
    }
    
    public function getFullName(): string {
        return $this->firstName . ' ' . $this->lastName;
    }
    
    public function verifyPassword(string $password): bool {
        return password_verify($password, $this->password);
    }
    
    public function isAdmin(): bool {
        return $this->role === 'admin';
    }
    
    public function isHost(): bool {
        return $this->role === 'hote' || $this->role === 'admin';
    }
    
    public function isVoyageur(): bool {
        return $this->role === 'voyageur';
    }
    
    // Getters
    public function getId(): ?int { return $this->id; }
    public function getEmail(): string { return $this->email; }
    public function getPassword(): string { return $this->password; }
    public function getFirstName(): string { return $this->firstName; }
    public function getLastName(): string { return $this->lastName; }
    public function getRole(): string { return $this->role; }
    public function getPhone(): ?string { return $this->phone; }
    public function isActive(): bool { return $this->isActive; }
    public function getCreatedAt(): ?\DateTime { return $this->createdAt; }
    
    // Setters
    public function setId(int $id): void { $this->id = $id; }
    public function setEmail(string $email): void { $this->email = $email; }
    public function setPassword(string $password): void {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }
    public function setFirstName(string $firstName): void { $this->firstName = $firstName; }
    public function setLastName(string $lastName): void { $this->lastName = $lastName; }
    public function setRole(string $role): void { $this->role = $role; }
    public function setPhone(?string $phone): void { $this->phone = $phone; }
    public function setIsActive(bool $isActive): void { $this->isActive = $isActive; }
}