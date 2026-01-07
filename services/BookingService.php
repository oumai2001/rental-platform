<?php
namespace Services;

use Interfaces\CancellableInterface;
use Repositories\BookingRepository;
use Repositories\RentalRepository;
use Models\Booking;
use Exceptions\{UnauthorizedException, BookingConflictException};

class BookingService implements CancellableInterface {
    protected BookingRepository $bookingRepo;
    protected RentalRepository $rentalRepo;

    public function __construct() {
        $this->bookingRepo = new BookingRepository();
        $this->rentalRepo = new RentalRepository();
    }

    // إنشاء حجز
    public function createBooking(array $data, int $userId): Booking {
        $rental = $this->rentalRepo->findById($data['rental_id']);
        if (!$rental || !$rental->isActive()) {
            throw new BookingConflictException("Ce logement n'est pas disponible");
        }

        $booking = new Booking([
            'rental_id' => $data['rental_id'],
            'user_id' => $userId,
            'check_in' => $data['check_in'],
            'check_out' => $data['check_out'],
            'number_of_guests' => $data['number_of_guests'],
            'total_price' => 0,
            'status' => 'confirmed'
        ]);

        $booking->calculateTotalPrice($rental->getPricePerNight());

        return $this->bookingRepo->create($booking);
    }

    // إلغاء الحجز
    public function cancel(int $bookingId, int $userId): bool {
        $booking = $this->bookingRepo->findById($bookingId);
        if ($booking->getUserId() !== $userId) {
            throw new UnauthorizedException("Vous ne pouvez annuler que vos propres réservations");
        }
        return $this->bookingRepo->cancel($bookingId);
    }

    // استرجاع حجوزات المستخدم
    public function getUserBookings(int $userId): array {
        return $this->bookingRepo->findByUserId($userId);
    }

    // استرجاع حجز بالـ ID
    public function getBookingById(int $bookingId): Booking {
        return $this->bookingRepo->findById($bookingId);
    }

    // استرجاع جميع الحجوزات
    public function getAllBookings(): array {
        return $this->bookingRepo->findAll();
    }
}
