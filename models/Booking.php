<?php
namespace Models;

class Booking {
    private ?int $id = null;
    private int $rentalId;
    private int $userId;
    private \DateTime $checkIn;
    private \DateTime $checkOut;
    private float $totalPrice;
    private int $numberOfGuests;
    private string $status = 'confirmed';
    private ?\DateTime $createdAt = null;
    
    public function __construct(?array $data = null) {
        if ($data) {
            $this->id = $data['id'] ?? null;
            $this->rentalId = $data['rental_id'];
            $this->userId = $data['user_id'];
            $this->checkIn = new \DateTime($data['check_in']);
            $this->checkOut = new \DateTime($data['check_out']);
            $this->totalPrice = (float)$data['total_price'];
            $this->numberOfGuests = (int)$data['number_of_guests'];
            $this->status = $data['status'] ?? 'confirmed';
            $this->createdAt = isset($data['created_at']) ? new \DateTime($data['created_at']) : null;
        }
    }
    
    public function validate(): void {
        if ($this->checkIn >= $this->checkOut) {
            throw new \Exception("La date de départ doit être après la date d'arrivée");
        }
        
        if ($this->checkIn < new \DateTime('today')) {
            throw new \Exception("La date d'arrivée ne peut pas être dans le passé");
        }
        
        if ($this->numberOfGuests < 1) {
            throw new \Exception("Le nombre de voyageurs doit être au moins 1");
        }
    }
    
    public function calculateTotalPrice(float $pricePerNight): void {
        $nights = $this->getNights();
        $this->totalPrice = $nights * $pricePerNight;
    }
    
    public function getNights(): int {
        return $this->checkIn->diff($this->checkOut)->days;
    }
    
    // Getters
    public function getId(): ?int { return $this->id; }
    public function getRentalId(): int { return $this->rentalId; }
    public function getUserId(): int { return $this->userId; }
    public function getCheckIn(): \DateTime { return $this->checkIn; }
    public function getCheckOut(): \DateTime { return $this->checkOut; }
    public function getTotalPrice(): float { return $this->totalPrice; }
    public function getNumberOfGuests(): int { return $this->numberOfGuests; }
    public function getStatus(): string { return $this->status; }
    public function getCreatedAt(): ?\DateTime { return $this->createdAt; }
    
    // Setters
    public function setId(int $id): void { $this->id = $id; }
    public function setRentalId(int $rentalId): void { $this->rentalId = $rentalId; }
    public function setUserId(int $userId): void { $this->userId = $userId; }
    public function setCheckIn(\DateTime $checkIn): void { $this->checkIn = $checkIn; }
    public function setCheckOut(\DateTime $checkOut): void { $this->checkOut = $checkOut; }
    public function setTotalPrice(float $totalPrice): void { $this->totalPrice = $totalPrice; }
    public function setNumberOfGuests(int $numberOfGuests): void { $this->numberOfGuests = $numberOfGuests; }
    public function setStatus(string $status): void { $this->status = $status; }
}