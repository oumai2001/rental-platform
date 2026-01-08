<?php
namespace Exceptions;

class UnauthorizedException extends \Exception {
    public function __construct(string $message = "Unauthorized access", int $code = 401) {
        parent::__construct($message, $code);
    }
}