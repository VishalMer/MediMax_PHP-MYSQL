<?php
   
// add to cart functionality
if (isset($_POST['add_to_cart'])) {
    $product_name = $_POST['product_name'];
    $product_image = $_POST['product_image'];
    $product_price = $_POST['product_price'];
    $product_quantity = 1;

    $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE name = '$product_name' AND user_id = '$user_id'") or die('query failed');

    if (mysqli_num_rows($select_cart) > 0) {
        $message[] = 'Product already added to cart!';
    } else {
        mysqli_query($conn, "INSERT INTO `cart`(user_id, name, price, image, quantity) VALUES ('$user_id', '$product_name', '$product_price', '$product_image', '$product_quantity')") or die('query failed');
        $message[] = 'Product added to cart!';
    }
}

// Add to wishlist functionality
if (isset($_POST['add_to_wishlist'])) {
    $product_name = $_POST['product_name'];
    $product_image = $_POST['product_image'];
    $product_price = $_POST['product_price'];

    // Check if the product is already in the wishlist
    $select_wishlist = mysqli_query($conn, "SELECT * FROM `wishlist` WHERE name = '$product_name' AND user_id = '$user_id'") or die('query failed');

    if (mysqli_num_rows($select_wishlist) > 0) {
        // Product found in wishlist, so remove it
        mysqli_query($conn, "DELETE FROM `wishlist` WHERE name = '$product_name' AND user_id = '$user_id'") or die('query failed');
        $message[] = 'Product removed from wishlist!';
    } else {
        // Product not found in wishlist, so add it
        mysqli_query($conn, "INSERT INTO `wishlist` (user_id, name, price, image) VALUES ('$user_id', '$product_name','$product_price', '$product_image')") or die('query failed');
        $message[] = 'Product added to wishlist!';
    }
}

// Search functionality
$search_query = "";
if (isset($_POST['search'])) {
    $search_query = $_POST['search_input'];
}

?>