<?php
// PHP/products_buttons.php

// Ensure session is started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Assume $conn and $user_id are available from calling page (index.php or Products.php)

// --- CRITICAL CHANGE HERE: Handle login redirection FIRST ---
if ( (isset($_POST['add_to_cart']) || isset($_POST['add_to_wishlist'])) && !isset($user_id) ) {
    if (isset($_POST['add_to_cart'])) {
        $_SESSION['login_message'] = 'Please log in to add items to your cart.';
    } elseif (isset($_POST['add_to_wishlist'])) {
        $_SESSION['login_message'] = 'Please log in to add items to your wishlist.';
    }

    // Corrected Redirect Path:
    // If your project is in a subfolder like 'medimax', you need to include that in the path.
    // Example: If your site is accessed as http://localhost/medimax/
    header('Location: /medimax/PHP/login_form.php'); // <-- CORRECTED THIS LINE
    exit();
}
// --- END CRITICAL CHANGE ---


// If the script reaches here, it means either:
// 1. No add_to_cart/wishlist action was requested.
// 2. An add_to_cart/wishlist action was requested AND the user IS logged in ($user_id is set).

if (isset($_POST['add_to_cart']) || isset($_POST['add_to_wishlist'])) {

    // Common product details
    $product_name = filter_input(INPUT_POST, 'product_name', FILTER_SANITIZE_STRING);
    $product_price = filter_input(INPUT_POST, 'product_price', FILTER_VALIDATE_FLOAT);
    $product_image = filter_input(INPUT_POST, 'product_image', FILTER_SANITIZE_STRING);
    $product_quantity = 1;

    // Validate inputs
    if (empty($product_name) || !is_numeric($product_price) || empty($product_image)) {
        $_SESSION['message'][] = 'Error: Missing product details for adding.';
        // This redirect needs to consider the calling page too.
        // For simplicity, if it's always coming from products page in PHP folder or index,
        // and you want to go back to products listing:
        // header('Location: products.php'); // if products.php is in the same PHP folder
        // OR
        header('Location: ' . $_SERVER['HTTP_REFERER']); // Redirect back to the page it came from (less reliable but often works)
        exit();
    }

    // Handle adding to cart for logged-in users
    if (isset($_POST['add_to_cart'])) {
        $product_name_db = mysqli_real_escape_string($conn, $product_name);
        $product_image_db = mysqli_real_escape_string($conn, $product_image);

        $check_cart_numbers = mysqli_query($conn, "SELECT * FROM `cart` WHERE name = '$product_name_db' AND user_id = '$user_id'") or die('Query failed');
        if (mysqli_num_rows($check_cart_numbers) > 0) {
            $_SESSION['message'][] = 'Already added to cart!';
        } else {
            mysqli_query($conn, "INSERT INTO `cart` (user_id, name, price, quantity, image) VALUES ('$user_id', '$product_name_db', '$product_price', '$product_quantity', '$product_image_db')") or die('Query failed');
            $_SESSION['message'][] = 'Product added to cart!';
        }
        // Redirect back to the referring page or a known product listing page
        header('Location: ' . $_SERVER['HTTP_REFERER']); // This will send them back to index.php or Products.php
        exit();
    }

    // Handle adding to wishlist for logged-in users
    if (isset($_POST['add_to_wishlist'])) {
        $product_name_db = mysqli_real_escape_string($conn, $product_name);
        $product_image_db = mysqli_real_escape_string($conn, $product_image);

        $check_wishlist_numbers = mysqli_query($conn, "SELECT * FROM `wishlist` WHERE name = '$product_name_db' AND user_id = '$user_id'") or die('Query failed');
        if (mysqli_num_rows($check_wishlist_numbers) > 0) {
            mysqli_query($conn, "DELETE FROM `wishlist` WHERE name = '$product_name_db' AND user_id = '$user_id'") or die('Query failed');
            $_SESSION['message'][] = 'Product removed from wishlist!';
        } else {
            mysqli_query($conn, "INSERT INTO `wishlist` (user_id, name, price, image) VALUES ('$user_id', '$product_name_db', '$product_price', '$product_image_db')") or die('Query failed');
            $_SESSION['message'][] = 'Product added to wishlist!';
        }
        // Redirect back to the referring page
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
}
?>