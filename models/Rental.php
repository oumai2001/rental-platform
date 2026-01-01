<?php
namespace Models;

class Rental {
    private ?int $id = null;
    private int $hostId;
    private string $title;
    private string $description;
    private string $city;
    private string $address;
    private float $pricePerNight;
    private int $maxGuests;
    private int $bedrooms;
    private int $bathrooms;
    private bool $isActive = true;
    private float $averageRating = 0;
    private ?\DateTime $createdAt = null;
    
    public function __construct(?array $data = null) {
        if ($data) {
            $this->id = $data['id'] ?? null;
            $this->hostId = $data['host_id'];
            $this->title = $data['title'];
            $this->description = $data['description'];
            $this->city = $data['city'];
            $this->address = $data['address'];
            $this->pricePerNight = (float)$data['price_per_night'];
            $this->maxGuests = (int)$data['max_guests'];
            $this->bedrooms = (int)$data['bedrooms'];
            $this->bathrooms = (int)$data['bathrooms'];
            $this->isActive = (bool)($data['is_active'] ?? true);
            $this->averageRating = (float)($data['average_rating'] ?? 0);
            $this->createdAt = isset($data['created_at']) ? new \DateTime($data['created_at']) : null;
        }
    }
    
    // Getters
    public function getId(): ?int { return $this->id; }
    public function getHostId(): int { return $this->hostId; }
    public function getTitle(): string { return $this->title; }
    public function getDescription(): string { return $this->description; }
    public function getCity(): string { return $this->city; }
    public function getAddress(): string { return $this->address; }
    public function getPricePerNight(): float { return $this->pricePerNight; }
    public function getMaxGuests(): int { return $this->maxGuests; }
    public function getBedrooms(): int { return $this->bedrooms; }
    public function getBathrooms(): int { return $this->bathrooms; }
    public function isActive(): bool { return $this->isActive; }
    public function getAverageRating(): float { return $this->averageRating; }
    public function getCreatedAt(): ?\DateTime { return $this->createdAt; }
    
    // Setters
    public function setId(int $id): void { $this->id = $id; }
    public function setHostId(int $hostId): void { $this->hostId = $hostId; }
    public function setTitle(string $title): void { $this->title = $title; }
    public function setDescription(string $description): void { $this->description = $description; }
    public function setCity(string $city): void { $this->city = $city; }
    public function setAddress(string $address): void { $this->address = $address; }
    public function setPricePerNight(float $pricePerNight): void { $this->pricePerNight = $pricePerNight; }
    public function setMaxGuests(int $maxGuests): void { $this->maxGuests = $maxGuests; }
    public function setBedrooms(int $bedrooms): void { $this->bedrooms = $bedrooms; }
    public function setBathrooms(int $bathrooms): void { $this->bathrooms = $bathrooms; }
    public function setIsActive(bool $isActive): void { $this->isActive = $isActive; }
    public function setAverageRating(float $averageRating): void { $this->averageRating = $averageRating; }
}