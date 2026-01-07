<?php
namespace Services;

/**
 * AdminBookingService - Polymorphism Implementation
 * 
 * This service extends BookingService but overrides the cancel method
 * to allow admins to cancel any booking, not just their own.
 * 
 * This demonstrates polymorphism:
 * - Same method signature (cancel)
 * - Different behavior based on context (admin vs regular user)
 */
class AdminBookingService extends BookingService {
    
    /**
     * Admin can cancel ANY booking
     * Overrides parent method to remove user ownership check
     */
    public function cancel(int $bookingId, int $userId): bool {
        $booking = $this->bookingRepo->findById($bookingId);
        
        // Admin doesn't need to own the booking
        $result = $this->bookingRepo->cancel($bookingId);
        
        if ($result) {
            // Notify the booking owner, not the admin
            $this->emailService->sendCancellationNotification(
                $booking->getUserId(), 
                $bookingId
            );
        }
        
        return $result;
    }
    
 
    public function cancelWithReason(int $bookingId, string $reason): bool {
        $booking = $this->bookingRepo->findById($bookingId);
        $result = $this->bookingRepo->cancel($bookingId);
        
        if ($result) {
            $this->emailService->sendAdminCancellationNotification(
                $booking->getUserId(), 
                $bookingId,
                $reason 
            );
        }
        
        return $result;
    }
}