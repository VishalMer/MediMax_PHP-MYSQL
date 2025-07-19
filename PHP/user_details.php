<?php
session_start(); // Essential if this file is accessed directly for logout

// --- Crucial Cache Control Headers for this script only if it outputs anything ---
// If it only redirects, these are less critical, but good practice.
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

// Check if a logout request has been made
if (isset($_GET['logout'])) {
    // Clear all session variables
    $_SESSION = array();

    // Destroy the session
    session_destroy();

    // Redirect to the login page after logout
    header('Location: PHP/login_form.php'); // Make sure this path is correct
    exit(); // Always exit after a header redirect
}

// If this file is included by index.php and no logout is requested, it does nothing.
// Any user detail fetching should be handled by the including script (index.php).
?>