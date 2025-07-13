<?php
    session_start();
    $user_id = $_SESSION['user_id'];
    
    if (!isset($user_id)) {
        header('Location: login_form.php');
    }
    
    // Fetch the details of the user by user id
    $select_user = mysqli_query($conn, "SELECT * FROM `users` WHERE id = '$user_id'") or die('Query failed');
    if (mysqli_num_rows($select_user) > 0) {
        $fetch_user = mysqli_fetch_assoc($select_user);
    }
    
    //fetch user's info
    $username = $fetch_user['name'];
    $firstLetter = strtoupper($username[0]);
    $user_role = $fetch_user['role'];
    $user_image = $fetch_user['image'];   
?>

