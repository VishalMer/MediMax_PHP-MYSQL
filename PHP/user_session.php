<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$user_id = null;
$fetch_user = null;
$username = 'Guest';
$firstLetter = 'G';
$user_role = 'guest';
$user_image = '';
$user_email = '';

if (isset($conn) && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $select_user = mysqli_query($conn, "SELECT * FROM `users` WHERE id = '$user_id'") or die('User query failed: ' . mysqli_error($conn));
    if (mysqli_num_rows($select_user) > 0) {
        $fetch_user = mysqli_fetch_assoc($select_user);
        $username = $fetch_user['name'];
        $firstLetter = strtoupper($username[0]);
        $user_role = $fetch_user['role'];
        $user_image = $fetch_user['image'];
        $user_email = $fetch_user['email'];
    } else {
        session_unset();
        session_destroy();
        header('Location: ./login_form.php?logout=true');
        exit();
    }
} else if (!isset($conn)) {
    error_log("Database connection (\$conn) not available for user session in " . __FILE__);
}

if (isset($_GET['logout'])) {
    $_SESSION = array(); 
    session_destroy();
    
    header('Location: /medimax/PHP/login_form.php'); 
    exit();
}


?>