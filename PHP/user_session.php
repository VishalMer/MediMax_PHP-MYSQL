<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// user variables - Initialize them always
$user_id = null;
$fetch_user = null;
$username = 'Guest';
$firstLetter = 'G';
$user_role = 'guest';
$user_image = '';

// Check if $conn exists (it should be included before user_session.php if it's used here)
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
        // Redirect to login form. Make sure this header is not preceded by any output.
        header('Location: login_form.php?logout=true');
        exit();
    }
} else if (!isset($conn)) {
    // if database connection is missing - this is an error, log it
    error_log("Database connection (\$conn) not available for user session in " . __FILE__);
    // Optionally: force logout or set a message here if database is completely inaccessible
}

// Handle logout request (this should be the only redirect logic in user_session.php)
if (isset($_GET['logout'])) {
    $_SESSION = array(); // Clear all session data
    session_destroy();
    // Redirect. Make sure this header is not preceded by any output.
    header('Location: login_form.php'); // Or '../index.php' if that's your logout destination
    exit();
}

// IMPORTANT: Do NOT initialize or clear $_SESSION['message'] or $_SESSION['login_message'] here.
// These will be handled by the specific pages that display them.

?>