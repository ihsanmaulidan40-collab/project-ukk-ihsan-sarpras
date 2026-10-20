<?php
require_once __DIR__ . '/includes/auth.php';

if (!empty($_SESSION['login'])) {
    header("Location: " . dashboard_url_for_role($_SESSION['role'] ?? ''));
} else {
    header("Location: login.php");
}
exit;
