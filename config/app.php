<?php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', '/rental-platform');
}

function url($path = '') {
    return BASE_PATH . $path;
}

function redirect($path) {
    header('Location: ' . url($path));
    exit;
}
