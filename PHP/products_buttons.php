<?php
// PHP/products_buttons.php

// Ensure session is started, it should be already by user_session.php, but good to double-check
// Make sure this is the ABSOLUTE FIRST THING in this file, no spaces or newlines before <?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// $conn and $user_id are expected to be available from products.php including connection.php and user_session.php before this file.

// --- CRITICAL CHANGE HERE: Handle login redirection FIRST if an add to cart/wishlist action is attempted by a non-logged-in user ---
// Check if EITHER add_to_cart OR add_to_wishlist button was clicked AND $user_id is NOT set.
// If $user_id is not set, it means the user is not logged in.
if ( (isset($_POST['add_to_cart']) || isset($_POST['add_to_wishlist'])) && !isset($user_id) ) {
    if (isset($_POST['add_to_cart'])) {
        $_SESSION['login_message'] = 'Please log in to add items to your cart.';
    } elseif (isset($_POST['add_to_wishlist'])) {
        $_SESSION['login_message'] = 'Please log in to add items to your wishlist.';
    }
    // Perform the redirect and immediately stop script execution
    header('Location: login_form.php'); // Ensure this path is correct if login_form.php is in the same dir as products.php
    exit(); // Crucial to prevent further output and ensure redirect
}
// --- END CRITICAL CHANGE ---


// If the script reaches here, it means either:
// 1. No add_to_cart/wishlist action was requested.
// 2. An add_to_cart/wishlist action was requested AND the user IS logged in ($user_id is set).

if (isset($_POST['add_to_cart']) || isset($_POST['add_to_wishlist'])) {

    // Common product details (only processed if user is logged in, as handled above)
    $product_name = filter_input(INPUT_POST, 'product_name', FILTER_SANITIZE_STRING);
    $product_price = filter_input(INPUT_POST, 'product_price', FILTER_VALIDATE_FLOAT);
    $product_image = filter_input(INPUT_POST, 'product_image', FILTER_SANITIZE_STRING);
    $product_quantity = 1; // Default quantity for adding to cart/wishlist

    // Validate inputs
    // This validation is for logged-in users attempting to add valid products
    if (empty($product_name) || !is_numeric($product_price) || empty($product_image)) {
        $_SESSION['message'][] = 'Error: Missing product details for adding.';
        header('Location: products.php'); // Redirect back to products page
        exit();
    }

    // Handle adding to cart for logged-in users
    if (isset($_POST['add_to_cart'])) {
        $check_cart_numbers = mysqli_query($conn, "SELECT * FROM `cart` WHERE name = '$product_name' AND user_id = '$user_id'") or die('Query failed');
        if (mysqli_num_rows($check_cart_numbers) > 0) {
            $_SESSION['message'][] = 'Already added to cart!';
        } else {
            mysqli_query($conn, "INSERT INTO `cart` (user_id, name, price, quantity, image) VALUES ('$user_id', '$product_name', '$product_price', '$product_quantity', '$product_image')") or die('Query failed');
            $_SESSION['message'][] = 'Product added to cart!';
        }
        header('Location: products.php'); // Always redirect to prevent re-submission
        exit();
    }

    // Handle adding to wishlist for logged-in users
    if (isset($_POST['add_to_wishlist'])) {
        $check_wishlist_numbers = mysqli_query($conn, "SELECT * FROM `wishlist` WHERE name = '$product_name' AND user_id = '$user_id'") or die('Query failed');
        if (mysqli_num_rows($check_wishlist_numbers) > 0) {
            $_SESSION['message'][] = 'Already added to wishlist!';
        } else {
            mysqli_query($conn, "INSERT INTO `wishlist` (user_id, name, price, image) VALUES ('$user_id', '$product_name', '$product_price', '$product_image')") or die('Query failed');
            $_SESSION['message'][] = 'Product added to wishlist!';
        }
        header('Location: products.php'); // Always redirect to prevent re-submission
        exit();
    }
}
?>