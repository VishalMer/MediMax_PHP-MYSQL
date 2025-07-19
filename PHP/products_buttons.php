<?php

include 'connection.php'; // Adjust path if necessary

if (!isset($_SESSION['message'])) {
    $_SESSION['message'] = [];
}

$user_id = null;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
}

// add to cart functionality
if (isset($_POST['add_to_cart'])) {
    if ($user_id === null) {
        $_SESSION['message'][] = 'Please login to add items to your cart!';
        header('Location: login_form.php');
        exit();
    }
    // ... rest of add to cart logic ...
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

    header('Location: ../index.php');
    exit();
}

// Add to wishlist functionality
if (isset($_POST['add_to_wishlist'])) {
    if ($user_id === null) {
        $_SESSION['message'][] = 'Please login to add items to your wishlist!';
        header('Location: login_form.php');
        exit();
    }
    // ... rest of add to wishlist logic ...
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

    header('Location: ../index.php');
    exit();
}

$search_query = "";
if (isset($_POST['search'])) {
    $search_query = htmlspecialchars($_POST['search_input']);
}

?>