<?php
namespace Services;

use Repositories\{UserRepository, RentalRepository, BookingRepository};

class StatisticsService {
    private UserRepository $userRepo;
    private RentalRepository $rentalRepo;
    private BookingRepository $bookingRepo;
    
    public function __construct() {
        $this->userRepo = new UserRepository();
        $this->rentalRepo = new RentalRepository();
        $this->bookingRepo = new BookingRepository();
    }
    
    public function getDashboardStats(): array {
        return [
            'total_users' => $this->userRepo->count(),
            'total_rentals' => $this->rentalRepo->count(),
            'total_bookings' => $this->bookingRepo->count(),
            'total_revenue' => $this->bookingRepo->getTotalRevenue(),
            'top_rentals' => $this->rentalRepo->getTopRentals(10)
        ];
    }
    
    public function getUserStats(int $userId): array {
        $bookings = $this->bookingRepo->findByUserId($userId);
        
        $totalBookings = count($bookings);
        $totalSpent = array_reduce($bookings, function($sum, $booking) {
            return $sum + ($booking->getStatus() === 'confirmed' ? $booking->getTotalPrice() : 0);
        }, 0);
        
        $confirmedBookings = array_filter($bookings, fn($b) => $b->getStatus() === 'confirmed');
        $cancelledBookings = array_filter($bookings, fn($b) => $b->getStatus() === 'cancelled');
        
        return [
            'total_bookings' => $totalBookings,
            'confirmed_bookings' => count($confirmedBookings),
            'cancelled_bookings' => count($cancelledBookings),
            'total_spent' => $totalSpent
        ];
    }
    
    public function getHostStats(int $hostId): array {
        $rentals = $this->rentalRepo->findByHostId($hostId);
        $totalRentals = count($rentals);
        
        $totalRevenue = 0;
        $totalBookings = 0;
        
        foreach ($rentals as $rental) {
            $bookings = $this->bookingRepo->findByRentalId($rental->getId());
            foreach ($bookings as $booking) {
                if ($booking->getStatus() === 'confirmed') {
                    $totalRevenue += $booking->getTotalPrice();
                    $totalBookings++;
                }
            }
        }
        
        return [
            'total_rentals' => $totalRentals,
            'total_bookings' => $totalBookings,
            'total_revenue' => $totalRevenue,
            'average_revenue_per_rental' => $totalRentals > 0 ? $totalRevenue / $totalRentals : 0
        ];
    }
}