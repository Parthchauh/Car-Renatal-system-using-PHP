<?php
/**
 * Auth API - Logout
 */

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';

header('Content-Type: application/json');

destroySession();
successResponse('Logged out successfully.');
