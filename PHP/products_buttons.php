<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ( (isset($_POST['add_to_cart']) || isset($_POST['add_to_wishlist'])) && !isset($user_id) ) {
    if (isset($_POST['add_to_cart'])) {
        $_SESSION['login_message'] = 'Please log in to add items to your cart.';
    } elseif (isset($_POST['add_to_wishlist'])) {
        $_SESSION['login_message'] = 'Please log in to add items to your wishlist.';
    }
    
    header('Location: /medimax/PHP/login_form.php'); 
    exit();
}

if (isset($_POST['add_to_cart']) || isset($_POST['add_to_wishlist'])) {

    // Common product details
    $product_name = filter_input(INPUT_POST, 'product_name', FILTER_SANITIZE_STRING);
    $product_price = filter_input(INPUT_POST, 'product_price', FILTER_VALIDATE_FLOAT);
    $product_image = filter_input(INPUT_POST, 'product_image', FILTER_SANITIZE_STRING);
    $product_quantity = 1;

    // Validate inputs
    if (empty($product_name) || !is_numeric($product_price) || empty($product_image)) {
        $_SESSION['message'][] = 'Error: Missing product details for adding.';
        
        header('Location: ' . $_SERVER['HTTP_REFERER']);
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

        header('Location: ' . $_SERVER['HTTP_REFERER']); 
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
        
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
}
?>