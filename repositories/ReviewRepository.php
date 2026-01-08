<?php
namespace Repositories;

use Config\Database;
use Models\Review;
use PDO;

class ReviewRepository {
    private PDO $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function create(Review $review): Review {
        $sql = "INSERT INTO reviews (rental_id, user_id, booking_id, rating, comment) 
                VALUES (:rental_id, :user_id, :booking_id, :rating, :comment)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':rental_id' => $review->getRentalId(),
            ':user_id' => $review->getUserId(),
            ':booking_id' => $review->getBookingId(),
            ':rating' => $review->getRating(),
            ':comment' => $review->getComment()
        ]);
        
        $review->setId((int)$this->db->lastInsertId());
        return $review;
    }
    
    public function findByRentalId(int $rentalId): array {
        $stmt = $this->db->prepare("SELECT * FROM reviews WHERE rental_id = :rental_id ORDER BY created_at DESC");
        $stmt->execute([':rental_id' => $rentalId]);
        
        $reviews = [];
        while ($data = $stmt->fetch()) {
            $reviews[] = new Review($data);
        }
        return $reviews;
    }
    
    public function findByUserId(int $userId): array {
        $stmt = $this->db->prepare("SELECT * FROM reviews WHERE user_id = :user_id ORDER BY created_at DESC");
        $stmt->execute([':user_id' => $userId]);
        
        $reviews = [];
        while ($data = $stmt->fetch()) {
            $reviews[] = new Review($data);
        }
        return $reviews;
    }
    
    public function calculateAverageRating(int $rentalId): float {
        $sql = "SELECT COALESCE(AVG(rating), 0) FROM reviews WHERE rental_id = :rental_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':rental_id' => $rentalId]);
        return (float)$stmt->fetchColumn();
    }
    
    public function existsForBooking(int $bookingId): bool {
        $sql = "SELECT COUNT(*) FROM reviews WHERE booking_id = :booking_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':booking_id' => $bookingId]);
        return $stmt->fetchColumn() > 0;
    }
}