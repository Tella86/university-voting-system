<?php
/**
 * Check if the current user is logged in.
 * @return bool
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if the logged-in user is an admin.
 * @return bool
 */
function is_admin() {
    return is_logged_in() && $_SESSION['role'] === 'admin';
}

/**
 * Check if the logged-in user is a voter.
 * @return bool
 */
function is_voter() {
    return is_logged_in() && $_SESSION['role'] === 'voter';
}

/**
 * Check if the logged-in user is a candidate.
 * @return bool
 */
function is_candidate() {
    return is_logged_in() && $_SESSION['role'] === 'candidate';
}

/**
 * Redirect to the login page if the user is not logged in.
 */
function require_login() {
    if (!is_logged_in()) {
        header("Location: ../login.php");
        exit;
    }
}

/**
 * Redirect to a 403 error page if the user does not have the correct role.
 * @param string $role Required role to access the page.
 */
function require_role($role) {
    if ($_SESSION['role'] !== $role) {
        header("HTTP/1.1 403 Forbidden");
        echo "Access denied. You do not have the required role to access this page.";
        exit;
    }
}
?>
