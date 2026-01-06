<?php
namespace Controllers;

use Repositories\RentalRepository;
use Models\Rental;
use Services\AuthService;

class RentalController {
    private RentalRepository $rentalRepo;
    private AuthService $authService;

    public function __construct() {
        $this->rentalRepo = new RentalRepository();
        $this->authService = new AuthService();
    }

    public function index(): void {
        $rentals = $this->rentalRepo->findAll();
        require_once __DIR__ . '/../views/rentals/index.php';
    }

    public function show(int $id): void {
        try {
            $rental = $this->rentalRepo->findById($id);
            require_once __DIR__ . '/../views/rentals/show.php';
        } catch (\Exception $e) {
            header('Location: ' . BASE_PATH . '/rentals');
            exit;
        }
    }

    public function create(): void {
        $user = $this->authService->requireAuth();

        if (!$user->isHost() && !$user->isAdmin()) {
            header('Location: ' . BASE_PATH . '/');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rental = new Rental();
            $rental->setHostId($user->getId());
            $rental->setTitle($_POST['title']);
            $rental->setDescription($_POST['description']);
            $rental->setCity($_POST['city']);
            $rental->setAddress($_POST['address']);
            $rental->setPricePerNight($_POST['price_per_night']);
            $rental->setMaxGuests($_POST['max_guests']);

            $this->rentalRepo->create($rental);

            header('Location: ' . BASE_PATH . '/my-rentals');
            exit;
        }

        require_once __DIR__ . '/../views/rentals/create.php';
    }

    public function myRentals(): void {
        $user = $this->authService->requireAuth();
        $rentals = $this->rentalRepo->findByHostId($user->getId());

        require_once __DIR__ . '/../views/rentals/my_rentals.php';
    }
}
