<?php
namespace Repositories;

use Config\Database;
use Models\Booking;
use Exceptions\{NotFoundException, BookingConflictException};
use PDO;

class BookingRepository {
    private PDO $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function create(Booking $booking): Booking {
        $booking->validate();
        
        if ($this->hasConflict($booking->getRentalId(), $booking->getCheckIn(), $booking->getCheckOut())) {
            throw new BookingConflictException("This rental is already booked for the selected dates");
        }
        
        $sql = "INSERT INTO bookings (rental_id, user_id, check_in, check_out, 
                total_price, number_of_guests, status) 
                VALUES (:rental_id, :user_id, :check_in, :check_out, 
                :total_price, :number_of_guests, :status)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':rental_id' => $booking->getRentalId(),
            ':user_id' => $booking->getUserId(),
            ':check_in' => $booking->getCheckIn()->format('Y-m-d'),
            ':check_out' => $booking->getCheckOut()->format('Y-m-d'),
            ':total_price' => $booking->getTotalPrice(),
            ':number_of_guests' => $booking->getNumberOfGuests(),
            ':status' => $booking->getStatus()
        ]);
        
        $booking->setId((int)$this->db->lastInsertId());
        return $booking;
    }
    
    public function hasConflict(int $rentalId, \DateTime $checkIn, \DateTime $checkOut, ?int $excludeBookingId = null): bool {
        $sql = "SELECT COUNT(*) FROM bookings 
                WHERE rental_id = :rental_id 
                AND status != 'cancelled'
                AND NOT (check_out <= :check_in OR check_in >= :check_out)";
        
        if ($excludeBookingId) {
            $sql .= " AND id != :booking_id";
        }
        
        $stmt = $this->db->prepare($sql);
        $params = [
            ':rental_id' => $rentalId,
            ':check_in' => $checkIn->format('Y-m-d'),
            ':check_out' => $checkOut->format('Y-m-d')
        ];
        
        if ($excludeBookingId) {
            $params[':booking_id'] = $excludeBookingId;
        }
        
        $stmt->execute($params);
        
        return $stmt->fetchColumn() > 0;
    }
    
    public function findById(int $id): Booking {
        $stmt = $this->db->prepare("SELECT * FROM bookings WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch();
        
        if (!$data) {
            throw new NotFoundException("Booking not found");
        }
        
        return new Booking($data);
    }
    
    public function findAll(): array {
        $stmt = $this->db->query("SELECT * FROM bookings ORDER BY created_at DESC");
        $bookings = [];
        while ($data = $stmt->fetch()) {
            $bookings[] = new Booking($data);
        }
        return $bookings;
    }
    
    public function findByUserId(int $userId): array {
        $stmt = $this->db->prepare("SELECT * FROM bookings WHERE user_id = :user_id ORDER BY created_at DESC");
        $stmt->execute([':user_id' => $userId]);
        
        $bookings = [];
        while ($data = $stmt->fetch()) {
            $bookings[] = new Booking($data);
        }
        return $bookings;
    }
    
    public function findByRentalId(int $rentalId): array {
        $stmt = $this->db->prepare("SELECT * FROM bookings WHERE rental_id = :rental_id ORDER BY check_in DESC");
        $stmt->execute([':rental_id' => $rentalId]);
        
        $bookings = [];
        while ($data = $stmt->fetch()) {
            $bookings[] = new Booking($data);
        }
        return $bookings;
    }
    
    public function cancel(int $bookingId): bool {
        $sql = "UPDATE bookings SET status = 'cancelled' WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $bookingId]);
    }
    
    public function count(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
    }
    
    public function getTotalRevenue(): float {
        $sql = "SELECT COALESCE(SUM(total_price), 0) FROM bookings WHERE status = 'confirmed'";
        return (float)$this->db->query($sql)->fetchColumn();
    }
}