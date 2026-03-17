<?php
/**
 * Root Router
 */
require_once __DIR__ . '/includes/session.php';

if (isLoggedIn()) {
    header('Location: views/index.php');
} else {
    header('Location: views/login.php');
}
exit;
