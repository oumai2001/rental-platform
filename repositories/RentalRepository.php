<?php
namespace Repositories;

use Config\Database;
use Models\Rental;
use Exceptions\NotFoundException;
use PDO;

class RentalRepository {
    private PDO $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function create(Rental $rental): Rental {
        $sql = "INSERT INTO rentals (host_id, title, description, city, address, 
                price_per_night, max_guests, bedrooms, bathrooms) 
                VALUES (:host_id, :title, :description, :city, :address, 
                :price_per_night, :max_guests, :bedrooms, :bathrooms)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':host_id' => $rental->getHostId(),
            ':title' => $rental->getTitle(),
            ':description' => $rental->getDescription(),
            ':city' => $rental->getCity(),
            ':address' => $rental->getAddress(),
            ':price_per_night' => $rental->getPricePerNight(),
            ':max_guests' => $rental->getMaxGuests(),
            ':bedrooms' => $rental->getBedrooms(),
            ':bathrooms' => $rental->getBathrooms()
        ]);
        
        $rental->setId((int)$this->db->lastInsertId());
        return $rental;
    }
    
    public function findById(int $id): Rental {
        $sql = "SELECT r.*, 
                COALESCE(AVG(rv.rating), 0) as average_rating
                FROM rentals r
                LEFT JOIN reviews rv ON r.id = rv.rental_id
                WHERE r.id = :id
                GROUP BY r.id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch();
        
        if (!$data) {
            throw new NotFoundException("Rental not found");
        }
        
        return new Rental($data);
    }
    
    public function findAll(int $limit = 20, int $offset = 0): array {
        $sql = "SELECT r.*, 
                COALESCE(AVG(rv.rating), 0) as average_rating
                FROM rentals r
                LEFT JOIN reviews rv ON r.id = rv.rental_id
                WHERE r.is_active = 1
                GROUP BY r.id
                LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        $rentals = [];
        while ($data = $stmt->fetch()) {
            $rentals[] = new Rental($data);
        }
        return $rentals;
    }
    
    public function search(array $filters, int $limit = 20, int $offset = 0): array {
        $sql = "SELECT r.*, 
                COALESCE(AVG(rv.rating), 0) as average_rating
                FROM rentals r
                LEFT JOIN reviews rv ON r.id = rv.rental_id
                WHERE r.is_active = 1";
        
        $params = [];
        
        if (!empty($filters['city'])) {
            $sql .= " AND r.city LIKE :city";
            $params[':city'] = '%' . $filters['city'] . '%';
        }
        
        if (isset($filters['min_price'])) {
            $sql .= " AND r.price_per_night >= :min_price";
            $params[':min_price'] = $filters['min_price'];
        }
        
        if (isset($filters['max_price'])) {
            $sql .= " AND r.price_per_night <= :max_price";
            $params[':max_price'] = $filters['max_price'];
        }
        
        if (isset($filters['min_guests'])) {
            $sql .= " AND r.max_guests >= :min_guests";
            $params[':min_guests'] = $filters['min_guests'];
        }
        
        $sql .= " GROUP BY r.id";
        
        if (isset($filters['check_in']) && isset($filters['check_out'])) {
            $sql = "SELECT * FROM (" . $sql . ") r 
                    WHERE r.id NOT IN (
                        SELECT rental_id FROM bookings 
                        WHERE status != 'cancelled'
                        AND NOT (check_out <= :check_in OR check_in >= :check_out)
                    )";
            $params[':check_in'] = $filters['check_in'];
            $params[':check_out'] = $filters['check_out'];
        }
        
        $sql .= " LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        $rentals = [];
        while ($data = $stmt->fetch()) {
            $rentals[] = new Rental($data);
        }
        return $rentals;
    }
    
    public function update(Rental $rental): bool {
        $sql = "UPDATE rentals SET title = :title, description = :description, 
                city = :city, address = :address, price_per_night = :price_per_night, 
                max_guests = :max_guests, bedrooms = :bedrooms, bathrooms = :bathrooms 
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $rental->getId(),
            ':title' => $rental->getTitle(),
            ':description' => $rental->getDescription(),
            ':city' => $rental->getCity(),
            ':address' => $rental->getAddress(),
            ':price_per_night' => $rental->getPricePerNight(),
            ':max_guests' => $rental->getMaxGuests(),
            ':bedrooms' => $rental->getBedrooms(),
            ':bathrooms' => $rental->getBathrooms()
        ]);
    }
    
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM rentals WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
    
    public function toggleActive(int $rentalId): bool {
        $sql = "UPDATE rentals SET is_active = NOT is_active WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $rentalId]);
    }
    
    public function findByHostId(int $hostId): array {
        $stmt = $this->db->prepare("SELECT * FROM rentals WHERE host_id = :host_id");
        $stmt->execute([':host_id' => $hostId]);
        
        $rentals = [];
        while ($data = $stmt->fetch()) {
            $rentals[] = new Rental($data);
        }
        return $rentals;
    }
    
    public function count(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM rentals")->fetchColumn();
    }
    
    public function getTopRentals(int $limit = 10): array {
        $sql = "SELECT r.id, r.title, r.city, 
                SUM(b.total_price) as total_revenue,
                COUNT(b.id) as booking_count
                FROM rentals r
                JOIN bookings b ON r.id = b.rental_id
                WHERE b.status = 'confirmed'
                GROUP BY r.id
                ORDER BY total_revenue DESC
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
}