<?php
require_once __DIR__ . '/../config.php';

function is_admin_logged_in(): bool {
    return !empty($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function require_admin_auth(): void {
    if (!is_admin_logged_in()) {
        header('Location: login.php');
        exit;
    }
}
