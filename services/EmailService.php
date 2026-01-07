<?php
namespace Services;

use Repositories\{UserRepository, BookingRepository, RentalRepository};

class EmailService {
    private UserRepository $userRepo;
    private BookingRepository $bookingRepo;
    private RentalRepository $rentalRepo;
    
    public function __construct() {
        $this->userRepo = new UserRepository();
        $this->bookingRepo = new BookingRepository();
        $this->rentalRepo = new RentalRepository();
    }
    
    public function sendBookingConfirmation(int $userId, int $bookingId): void {
        $user = $this->userRepo->findById($userId);
        $booking = $this->bookingRepo->findById($bookingId);
        $rental = $this->rentalRepo->findById($booking->getRentalId());
        
        $subject = "Confirmation de votre réservation #" . $bookingId;
        $message = "Bonjour {$user->getFullName()},\n\n";
        $message .= "Votre réservation a été confirmée avec succès.\n\n";
        $message .= "Détails de la réservation:\n";
        $message .= "Logement: {$rental->getTitle()}\n";
        $message .= "Ville: {$rental->getCity()}\n";
        $message .= "Check-in: {$booking->getCheckIn()->format('d/m/Y')}\n";
        $message .= "Check-out: {$booking->getCheckOut()->format('d/m/Y')}\n";
        $message .= "Nombre de nuits: {$booking->getNights()}\n";
        $message .= "Voyageurs: {$booking->getNumberOfGuests()}\n";
        $message .= "Montant total: {$booking->getTotalPrice()} MAD\n\n";
        $message .= "Merci de votre confiance!\n";
        $message .= "L'équipe RentHub";
        
        $this->send($user->getEmail(), $subject, $message);
    }
    
    public function sendCancellationNotification(int $userId, int $bookingId): void {
        $user = $this->userRepo->findById($userId);
        
        $subject = "Annulation de votre réservation #" . $bookingId;
        $message = "Bonjour {$user->getFullName()},\n\n";
        $message .= "Votre réservation #{$bookingId} a été annulée.\n\n";
        $message .= "Si vous avez des questions, n'hésitez pas à nous contacter.\n\n";
        $message .= "Cordialement,\n";
        $message .= "L'équipe RentHub";
        
        $this->send($user->getEmail(), $subject, $message);
    }
    
    public function sendAdminCancellationNotification(int $userId, int $bookingId, string $reason): void {
        $user = $this->userRepo->findById($userId);
        
        $subject = "Annulation de votre réservation #" . $bookingId;
        $message = "Bonjour {$user->getFullName()},\n\n";
        $message .= "Votre réservation #{$bookingId} a été annulée par l'administration.\n\n";
        $message .= "Raison: {$reason}\n\n";
        $message .= "Pour plus d'informations, veuillez nous contacter.\n\n";
        $message .= "Cordialement,\n";
        $message .= "L'équipe RentHub";
        
        $this->send($user->getEmail(), $subject, $message);
    }
    
    public function sendNewBookingNotification(int $hostId, int $bookingId): void {
        $host = $this->userRepo->findById($hostId);
        $booking = $this->bookingRepo->findById($bookingId);
        $rental = $this->rentalRepo->findById($booking->getRentalId());
        $guest = $this->userRepo->findById($booking->getUserId());
        
        $subject = "Nouvelle réservation pour " . $rental->getTitle();
        $message = "Bonjour {$host->getFullName()},\n\n";
        $message .= "Vous avez reçu une nouvelle réservation!\n\n";
        $message .= "Logement: {$rental->getTitle()}\n";
        $message .= "Voyageur: {$guest->getFullName()}\n";
        $message .= "Check-in: {$booking->getCheckIn()->format('d/m/Y')}\n";
        $message .= "Check-out: {$booking->getCheckOut()->format('d/m/Y')}\n";
        $message .= "Voyageurs: {$booking->getNumberOfGuests()}\n";
        $message .= "Montant: {$booking->getTotalPrice()} MAD\n\n";
        $message .= "Connectez-vous pour voir les détails.\n\n";
        $message .= "L'équipe RentHub";
        
        $this->send($host->getEmail(), $subject, $message);
    }
    
    private function send(string $to, string $subject, string $message): void {
        $headers = "From: noreply@renthub.ma\r\n";
        $headers .= "Reply-To: support@renthub.ma\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        
        // In production, use a proper email library like PHPMailer or SwiftMailer
        mail($to, $subject, $message, $headers);
        
        // Log email for debugging
        error_log("Email sent to {$to}: {$subject}");
    }
}