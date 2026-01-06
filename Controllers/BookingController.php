<?php
namespace Controllers;

use Services\{AuthService, BookingService};
use Models\Booking;

class BookingController
{
    private string $basePath;
    private BookingService $bookingService;
    private AuthService $authService;

    public function __construct() {
        $this->basePath = '/rental-platform';
        $this->bookingService = new BookingService();
        $this->authService = new AuthService();
    }

    // إنشاء حجز عبر الفورم
    public function create(): void {
        $user = $this->authService->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $booking = $this->bookingService->createBooking([
                    'rental_id' => (int)$_POST['rental_id'],
                    'check_in' => $_POST['check_in'],
                    'check_out' => $_POST['check_out'],
                    'number_of_guests' => (int)$_POST['number_of_guests']
                ], $user->getId());

                $_SESSION['success'] = "Réservation confirmée";
                header('Location: ' . $this->basePath . '/my-bookings');
                exit;
            } catch (\Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header('Location: ' . $this->basePath . '/rentals/' . $_POST['rental_id']);
                exit;
            }
        }
    }

    // عرض جميع حجوزات المستخدم
    public function myBookings(): void {
        $user = $this->authService->requireAuth();
        $bookings = $this->bookingService->getUserBookings($user->getId());
        require __DIR__ . '/../views/bookings/my_bookings.php';
    }
}
