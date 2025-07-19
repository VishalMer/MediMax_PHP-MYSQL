<?php
// PHP/products_buttons.php

// This file is intended to be included by main pages (like index.php or products.php)
// which should have already included 'connection.php' and 'user_session.php'.
// Therefore, $conn and $user_id (if logged in) are expected to be available.
// The $message array is also managed by user_session.php via $_SESSION['message'].

// No need for session_start() here as it's handled by user_session.php
// No need to initialize $_SESSION['message'] here, user_session.php does it.
// No need to fetch $user_id here, user_session.php does it.

// Check if $conn is available; it should be included by the calling script
if (!isset($conn)) {
    // Log an error or handle the missing connection gracefully
    error_log("Database connection (\$conn) not available in products_buttons.php.");
    // Optionally, set a session message and exit if DB is crucial for processing
    // $_SESSION['message'][] = 'System error: Database connection failed.';
    // exit();
}


// add to cart functionality
if (isset($_POST['add_to_cart'])) {
    // Check $user_id (provided by user_session.php)
    if ($user_id === null) {
        $_SESSION['message'][] = 'Please login to add items to your cart!';
        // Redirect to login form. Since this file is in PHP/ and login_form.php is also in PHP/,
        // and if index.php (in root) is submitting the form to itself (index.php),
        // then the redirect from products_buttons.php (included by index.php) should point to the correct path.
        // Assuming login_form.php is in the same directory as products_buttons.php (PHP/).
        // The header location needs to be relative to the *document root* if the main page is in root.
        // So, if index.php is in root, and products_buttons.php is included, this path will be relative to root.
        header('Location: PHP/login_form.php');
        exit();
    }
    
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $product_image = mysqli_real_escape_string($conn, $_POST['product_image']);
    $product_price = mysqli_real_escape_string($conn, $_POST['product_price']);
    $product_quantity = 1;

    $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE name = '$product_name' AND user_id = '$user_id'") or die('query failed: ' . mysqli_error($conn));

    if (mysqli_num_rows($select_cart) > 0) {
        $_SESSION['message'][] = 'Product already added to cart!';
    } else {
        mysqli_query($conn, "INSERT INTO `cart`(user_id, name, price, image, quantity) VALUES ('$user_id', '$product_name', '$product_price', '$product_image', '$product_quantity')") or die('query failed: ' . mysqli_error($conn));
        $_SESSION['message'][] = 'Product added to cart!';
    }
    // No redirect here, let the calling script (e.g., index.php) continue rendering
    // and display the messages.
}

// Add to wishlist functionality
if (isset($_POST['add_to_wishlist'])) {
    // Check $user_id (provided by user_session.php)
    if ($user_id === null) {
        $_SESSION['message'][] = 'Please login to add items to your wishlist!';
        header('Location: PHP/login_form.php'); // Corrected path assuming index.php is in root
        exit();
    }
    
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $product_image = mysqli_real_escape_string($conn, $_POST['product_image']);
    $product_price = mysqli_real_escape_string($conn, $_POST['product_price']);

    $select_wishlist = mysqli_query($conn, "SELECT * FROM `wishlist` WHERE name = '$product_name' AND user_id = '$user_id'") or die('query failed: ' . mysqli_error($conn));

    if (mysqli_num_rows($select_wishlist) > 0) {
        mysqli_query($conn, "DELETE FROM `wishlist` WHERE name = '$product_name' AND user_id = '$user_id'") or die('query failed: ' . mysqli_error($conn));
        $_SESSION['message'][] = 'Product removed from wishlist!';
    } else {
        mysqli_query($conn, "INSERT INTO `wishlist` (user_id, name, price, image) VALUES ('$user_id', '$product_name','$product_price', '$product_image')") or die('query failed: ' . mysqli_error($conn));
        $_SESSION['message'][] = 'Product added to wishlist!';
    }
    // No redirect here, let the calling script continue rendering.
}

// Removed the $search_query logic from here. It belongs in the main display page (like index.php or products.php)
// as it directly affects how products are displayed on that page, not a button action.

?>