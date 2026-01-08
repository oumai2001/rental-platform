<?php
namespace Exceptions;

class BookingConflictException extends \Exception {
    public function __construct(string $message = "Booking conflict detected", int $code = 409) {
        parent::__construct($message, $code);
    }
}