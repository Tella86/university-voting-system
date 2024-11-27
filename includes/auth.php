<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Role-based access control
function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function is_voter() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'voter';
}

function is_candidate() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'candidate';
}

// Redirect unauthorized access
if (strpos($_SERVER['SCRIPT_FILENAME'], 'admin') !== false && !is_admin()) {
    header("Location: ../login.php");
    exit;
}
