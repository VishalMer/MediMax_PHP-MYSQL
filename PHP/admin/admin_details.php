<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['logout'])) {
    $_SESSION = array(); 
    session_destroy(); 
    
    header('Location: /medimax/PHP/login_form.php'); 
    exit();
}


$user_id = $_SESSION['user_id'] ?? null; 

if (!isset($user_id)) {
    header('Location: ../login_form.php');
    exit();
}

$username = '';
$firstLetter = '';
$image = '';
$loggedInUserRole = ''; 
$fetch_user = []; 

$stmt = $conn->prepare("SELECT name, email, password, role, image FROM `users` WHERE id = ?");
                                         
if ($stmt === false) {
    error_log("Failed to prepare statement in admin_details.php: " . $conn->error);
    header('Location: ../error_page.php'); 
    exit();
}

$stmt->bind_param("i", $user_id); 
$stmt->execute();
$result = $stmt->get_result();

// Only fetch user if exists
if ($result->num_rows > 0) {
    $fetch_user = $result->fetch_assoc();
    $username = $fetch_user['name'];
    $firstLetter = strtoupper(substr($username, 0, 1));
    $image = $fetch_user['image']; 
    $loggedInUserRole = $fetch_user['role']; 

} else {
    session_unset();
    session_destroy();
    header('Location: ../login_form.php');
    exit();
}
$stmt->close(); 

// Get the name of the current script being executed
$current_page = basename($_SERVER['PHP_SELF']);


if (($loggedInUserRole !== 'admin' && $loggedInUserRole !== 'owner') && ($current_page !== 'update_profile.php' && $current_page !== 'update_password.php')) {
    
    header('Location: ../../index.php');
    exit();
}

?>