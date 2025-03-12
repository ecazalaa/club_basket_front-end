<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Define the session timeout duration in seconds (e.g., 1800 seconds = 30 minutes)
$session_timeout = 1800;

// Check if the 'last_activity' session variable is set
if (isset($_SESSION['last_activity'])) {
    // Calculate the inactive time
    $inactive_time = time() - $_SESSION['last_activity'];

    // If the inactive time exceeds the session timeout, destroy the session
    if ($inactive_time > $session_timeout) {
        session_unset(); // Unset all session variables
        session_destroy(); // Destroy the session
        header("Location: login.php"); // Redirect to the login page
        exit();
    }
}

// Update the 'last_activity' session variable with the current timestamp
$_SESSION['last_activity'] = time();
?>