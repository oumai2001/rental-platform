<?php
namespace Services;

use Repositories\{BookingRepository, RentalRepository, UserRepository};

class PDFService {
    private BookingRepository $bookingRepo;
    private RentalRepository $rentalRepo;
    private UserRepository $userRepo;
    
    public function __construct() {
        $this->bookingRepo = new BookingRepository();
        $this->rentalRepo = new RentalRepository();
        $this->userRepo = new UserRepository();
    }
    
    public function generateReceipt(int $bookingId): string {
        $booking = $this->bookingRepo->findById($bookingId);
        $rental = $this->rentalRepo->findById($booking->getRentalId());
        $user = $this->userRepo->findById($booking->getUserId());
        $host = $this->userRepo->findById($rental->getHostId());
        
        $html = $this->getReceiptHTML($booking, $rental, $user, $host);
        
        return $html;
    }
    
    private function getReceiptHTML($booking, $rental, $user, $host): string {
        $receiptNumber = str_pad($booking->getId(), 8, '0', STR_PAD_LEFT);
        $currentDate = date('d/m/Y');
        
        return "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <title>Reçu #{$receiptNumber}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 40px;
            max-width: 800px;
            margin: 0 auto;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #FF385C;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 32px;
            font-weight: bold;
            color: #FF385C;
            margin-bottom: 10px;
        }
        .receipt-number {
            font-size: 18px;
            color: #666;
        }
        .section {
            margin: 25px 0;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #FF385C;
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            padding: 8px 0;
        }
        .info-label {
            font-weight: 600;
            color: #666;
        }
        .info-value {
            color: #333;
        }
        .total-section {
            background: #f7f7f7;
            padding: 20px;
            margin-top: 30px;
            border-radius: 8px;
        }
        .total {
            display: flex;
            justify-content: space-between;
            font-size: 24px;
            font-weight: bold;
            color: #FF385C;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
        .status {
            display: inline-block;
            padding: 5px 15px;
            background: #d4edda;
            color: #155724;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class='header'>
        <div class='logo'>🏠 RentHub</div>
        <h1>REÇU DE RÉSERVATION</h1>
        <div class='receipt-number'>N° {$receiptNumber}</div>
        <div style='margin-top: 10px;'>
            <span class='status'>{$booking->getStatus()}</span>
        </div>
    </div>
    
    <div class='section'>
        <div class='section-title'>Informations Client</div>
        <div class='info-row'>
            <span class='info-label'>Nom complet:</span>
            <span class='info-value'>{$user->getFullName()}</span>
        </div>
        <div class='info-row'>
            <span class='info-label'>Email:</span>
            <span class='info-value'>{$user->getEmail()}</span>
        </div>
        <div class='info-row'>
            <span class='info-label'>Téléphone:</span>
            <span class='info-value'>{$user->getPhone()}</span>
        </div>
    </div>
    
    <div class='section'>
        <div class='section-title'>Détails du Logement</div>
        <div class='info-row'>
            <span class='info-label'>Logement:</span>
            <span class='info-value'>{$rental->getTitle()}</span>
        </div>
        <div class='info-row'>
            <span class='info-label'>Ville:</span>
            <span class='info-value'>{$rental->getCity()}</span>
        </div>
        <div class='info-row'>
            <span class='info-label'>Adresse:</span>
            <span class='info-value'>{$rental->getAddress()}</span>
        </div>
        <div class='info-row'>
            <span class='info-label'>Hôte:</span>
            <span class='info-value'>{$host->getFullName()}</span>
        </div>
    </div>
    
    <div class='section'>
        <div class='section-title'>Informations du Séjour</div>
        <div class='info-row'>
            <span class='info-label'>Date d'arrivée:</span>
            <span class='info-value'>{$booking->getCheckIn()->format('d/m/Y')}</span>
        </div>
        <div class='info-row'>
            <span class='info-label'>Date de départ:</span>
            <span class='info-value'>{$booking->getCheckOut()->format('d/m/Y')}</span>
        </div>
        <div class='info-row'>
            <span class='info-label'>Nombre de nuits:</span>
            <span class='info-value'>{$booking->getNights()}</span>
        </div>
        <div class='info-row'>
            <span class='info-label'>Nombre de voyageurs:</span>
            <span class='info-value'>{$booking->getNumberOfGuests()}</span>
        </div>
    </div>
    
    <div class='section'>
        <div class='section-title'>Détails Financiers</div>
        <div class='info-row'>
            <span class='info-label'>Prix par nuit:</span>
            <span class='info-value'>{$rental->getPricePerNight()} MAD</span>
        </div>
        <div class='info-row'>
            <span class='info-label'>Nombre de nuits:</span>
            <span class='info-value'>{$booking->getNights()}</span>
        </div>
    </div>
    
    <div class='total-section'>
        <div class='total'>
            <span>TOTAL</span>
            <span>{$booking->getTotalPrice()} MAD</span>
        </div>
    </div>
    
    <div class='footer'>
        <p>Document généré le {$currentDate}</p>
        <p>RentHub - Plateforme de location courte durée</p>
        <p>Email: contact@renthub.ma | Tél: +212 5 00 00 00 00</p>
        <p style='margin-top: 15px; font-size: 11px;'>
            Ce document constitue une preuve de réservation.<br>
            En cas de litige, veuillez contacter notre service client.
        </p>
    </div>
</body>
</html>";
    }
    
    public function downloadReceipt(int $bookingId): void {
        $html = $this->generateReceipt($bookingId);
        
        header('Content-Type: text/html; charset=utf-8');
        header('Content-Disposition: attachment; filename="recu_' . $bookingId . '.html"');
        
        echo $html;
    }
}