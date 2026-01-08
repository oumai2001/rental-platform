<?php
namespace Interfaces;

interface CancellableInterface {
    public function cancel(int $bookingId, int $userId): bool;
}