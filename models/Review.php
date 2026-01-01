<?php
namespace Models;

class Review {
    private ?int $id = null;
    private int $rentalId;
    private int $userId;
    private int $bookingId;
    private int $rating;
    private string $comment;
    private ?\DateTime $createdAt = null;
    
    public function __construct(?array $data = null) {
        if ($data) {
            $this->id = $data['id'] ?? null;
            $this->rentalId = $data['rental_id'];
            $this->userId = $data['user_id'];
            $this->bookingId = $data['booking_id'];
            $this->rating = (int)$data['rating'];
            $this->comment = $data['comment'];
            $this->createdAt = isset($data['created_at']) ? new \DateTime($data['created_at']) : null;
        }
    }
    
    public function validate(): void {
        if ($this->rating < 1 || $this->rating > 5) {
            throw new \Exception("La note doit être entre 1 et 5");
        }
        
        if (strlen($this->comment) < 10) {
            throw new \Exception("Le commentaire doit contenir au moins 10 caractères");
        }
    }
    
    // Getters
    public function getId(): ?int { return $this->id; }
    public function getRentalId(): int { return $this->rentalId; }
    public function getUserId(): int { return $this->userId; }
    public function getBookingId(): int { return $this->bookingId; }
    public function getRating(): int { return $this->rating; }
    public function getComment(): string { return $this->comment; }
    public function getCreatedAt(): ?\DateTime { return $this->createdAt; }
    
    // Setters
    public function setId(int $id): void { $this->id = $id; }
    public function setRentalId(int $rentalId): void { $this->rentalId = $rentalId; }
    public function setUserId(int $userId): void { $this->userId = $userId; }
    public function setBookingId(int $bookingId): void { $this->bookingId = $bookingId; }
    public function setRating(int $rating): void { $this->rating = $rating; }
    public function setComment(string $comment): void { $this->comment = $comment; }
}