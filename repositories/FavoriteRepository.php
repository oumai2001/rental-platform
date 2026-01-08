<?php
namespace Repositories;

use Config\Database;
use Models\Rental;
use PDO;

class FavoriteRepository {
    private PDO $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function add(int $userId, int $rentalId): bool {
        try {
            $sql = "INSERT INTO favorites (user_id, rental_id) VALUES (:user_id, :rental_id)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':user_id' => $userId, ':rental_id' => $rentalId]);
        } catch (\PDOException $e) {
            return false;
        }
    }
    
    public function remove(int $userId, int $rentalId): bool {
        $sql = "DELETE FROM favorites WHERE user_id = :user_id AND rental_id = :rental_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':user_id' => $userId, ':rental_id' => $rentalId]);
    }
    
    public function findByUserId(int $userId): array {
        $sql = "SELECT r.* FROM rentals r 
                JOIN favorites f ON r.id = f.rental_id 
                WHERE f.user_id = :user_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        
        $rentals = [];
        while ($data = $stmt->fetch()) {
            $rentals[] = new Rental($data);
        }
        return $rentals;
    }
    
    public function isFavorite(int $userId, int $rentalId): bool {
        $sql = "SELECT COUNT(*) FROM favorites WHERE user_id = :user_id AND rental_id = :rental_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId, ':rental_id' => $rentalId]);
        return $stmt->fetchColumn() > 0;
    }
    
    public function countByUser(int $userId): int {
        $sql = "SELECT COUNT(*) FROM favorites WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return (int)$stmt->fetchColumn();
    }
}