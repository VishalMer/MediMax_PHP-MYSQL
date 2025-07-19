<?php
// PHP/user_session.php

// Ensure session is started only if it hasn't been already
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// --- Crucial Cache Control Headers (Consider if this file should output anything) ---
// If this file *only* sets variables and redirects without outputting HTML,
// these headers are best placed in the main page (index.php, Cart.php, etc.)
// If you include this and it sometimes outputs before header redirects, keep them.
// For now, let's assume main pages will handle their own cache control.

// Initialize user-related variables
$user_id = null;
$fetch_user = null;
$username = 'Guest';
$firstLetter = 'G';
$user_role = 'guest';
$user_image = '';

// Database connection is assumed to be available via `include 'connection.php';`
// in the calling file before this file is included.
if (isset($conn) && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $select_user = mysqli_query($conn, "SELECT * FROM `users` WHERE id = '$user_id'") or die('User query failed: ' . mysqli_error($conn));
    if (mysqli_num_rows($select_user) > 0) {
        $fetch_user = mysqli_fetch_assoc($select_user);
        $username = $fetch_user['name'];
        $firstLetter = strtoupper($username[0]);
        $user_role = $fetch_user['role'];
        $user_image = $fetch_user['image'];
    } else {
        // User ID in session but not in DB, force logout
        session_unset();
        session_destroy();
        // Redirect to login form. Adjust path based on where user_session.php is included from.
        // If user_session.php is always included from files in the PHP/ directory (like Cart.php, Wishlist.php),
        // then 'login_form.php' is correct. If included from root (index.php), then 'PHP/login_form.php'.
        // Let's assume it's included from other PHP files within the same 'PHP/' folder.
        header('Location: login_form.php?logout=true');
        exit();
    }
} else if (!isset($conn)) {
    // Log an error if database connection is missing
    error_log("Database connection (\$conn) not available for user session in " . __FILE__);
    // Optionally, you might want to redirect or show an error page if DB connection is critical.
}

// Handle logout request if it comes via GET parameter
if (isset($_GET['logout'])) {
    // Clear all session variables
    $_SESSION = array();
    // Destroy the session
    session_destroy();
    // Redirect to the login page after logout
    // Adjust path based on where this file is included.
    // Assuming it's included from PHP/ folder for cart/wishlist or parent for index.php.
    // For universal use, you might need a more dynamic path or specific handling in each page.
    // For now, if index.php handles logout by including this, it should be 'PHP/login_form.php'.
    // If cart.php handles logout by including this, it should be 'login_form.php'.
    // A robust solution might involve redirecting to a known absolute path.
    // Let's keep it consistent with your index.php for now.
    header('Location: PHP/login_form.php'); // Assuming this is called from index.php level.
    exit();
}

// Initialize message array (used for user feedback)
if (!isset($_SESSION['message'])) {
    $_SESSION['message'] = [];
}
// Retrieve messages from session if any
$message = [];
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']); // Clear message after displaying
}

?>