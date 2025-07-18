<?php

include 'connection.php';
include 'user_details.php';
include 'products_buttons.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Products - MediMax.com</title>
</head>
<body>

    <header class="header">
        <a href="Index.php" class="logo">
            <img src="../Images/MediMax_Logo.jpg" alt="MediMax">
        </a>

        <div class="search-bar">
            <form method="post" action="">
                <input type="search" placeholder="Search MediFresh.in" name="search_input" id="search-input" value="<?php echo ($search_query); ?>">
                <button type="submit" name="search" class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
        </div>
        
        <nav class="nav">
           <a href="../index.php">Home</a>
           <a href="products.php" class="active">Products</a>
           <a href="Orders.php">Orders</a>
           <a href="AboutUs.php">About Us</a>
           <a href="Contact.php">Contact</a>
        </nav>

        <div class="profile">
            <button><a href="Wishlist.php"><i class="fa-solid fa-heart" style="color: #ff0000;"></i></a></button>
            <button><a href="Cart.php"><i class="fa-solid fa-cart-plus"></i></a></button>
            <button id="options">
                <div class="pr-pic">
                    <?php if (!empty($user_image)): ?>
                        <img src="./Images/<?php echo htmlspecialchars($user_image); ?>" alt="Profile Picture" style="width: 10px; height: 10px; margin-bottom: 1.5px; border-radius: 50%;">
                    <?php else: ?>
                        <span><?php echo $firstLetter; ?></span>
                    <?php endif; ?>
                </div>                <div id="userName"><?php echo ($fetch_user['name']); ?></div>
            </button>
        </div>
    </header>
        
    <!-- Profile Options -->
    <div class="pr-options hide">
        <button><a href="Update Profile.php">Update User Profile <i class="fa-solid fa-address-card" style="color: #ffffff;"></i></a></button><br>
        <button><a href="Update Password.php">Change Password <i class="fa-solid fa-key" style="color: #ffffff;"></i></a></button><br>
       
       <?php if ($user_role === 'admin' || $user_role === 'owner') { ?>
        <button><a href="AdminPanel.php" target="_blank">Admin Panel <i class="fa-solid fa-user-tie"></i></a></button><br>
        <?php } ?>

        <button>
            <a href="index.php?logout=<php echo $user_id; ?>" 
            onclick="return confirm('Are you sure you want to log out ??');">Log Out <i class="fa-solid fa-right-from-bracket" style="color: #ffffff;"></i></a>
        </button>
    </div>

        <br><br><br><br><br>
        
<!-- cart product adding message -->
<?php
if (isset($message)) {
    foreach ($message as $message) {
        echo '<div class="message" onclick="this.remove();">' . $message . '</div>';
    }
}
?>

<div class="shopping">
    <?php
            // Search products based on the search query
            if (!empty($search_query)) {
                $search_sql = mysqli_real_escape_string($conn, $search_query);
                $select_product = mysqli_query($conn, "SELECT * FROM `products` WHERE name LIKE '%$search_sql%'") or die('Query failed');
            } else {
                $select_product = mysqli_query($conn, "SELECT * FROM `products`") or die('Query failed');
            }

            if (mysqli_num_rows($select_product) > 0) {
                while ($fetch_product = mysqli_fetch_assoc($select_product)) {
                    // Check if the product is in the wishlist
                    $wishlist_check = mysqli_query($conn, "SELECT * FROM `wishlist` WHERE name = '{$fetch_product['name']}' AND user_id = '$user_id'") or die('Query failed');
                    $is_in_wishlist = mysqli_num_rows($wishlist_check) > 0;
            ?>
                    <form method="post" class="box" action="">
                        <h2><?php echo $fetch_product['name']; ?></h2>
                        <div class="box-img" style="background-image: url('../Images/<?php echo $fetch_product['image']; ?>')"></div>
                        <div class="box-bottom">
                            <p>Price: <i class="fa-solid fa-indian-rupee-sign"></i> <strong><?php echo $fetch_product['price']; ?></strong></p>
                            <button type="submit" name="add_to_cart" id="addToCart"><i class="fa-solid fa-cart-plus"></i></button>
                            <button type="submit" name="add_to_wishlist" id="add-wishlist" class="wishlist-btn">
                                <i class="<?php echo $is_in_wishlist ? 'fa-solid fa-heart' : 'fa-regular fa-heart'; ?>" 
                                   style="<?php echo $is_in_wishlist ? 'color: #ff0000;' : ''; ?>"></i>
                            </button>
                        </div><br>

                        <input type="hidden" name="product_image" value="<?php echo $fetch_product['image']; ?>">
                        <input type="hidden" name="product_name" value="<?php echo $fetch_product['name']; ?>">
                        <input type="hidden" name="product_price" value="<?php echo $fetch_product['price']; ?>">
                    </form>
            <?php
                }
            } else {
                echo "<p class='no-products'>No products found, We will make it available as soon as possible..</p>";
            }
            ?>
</div>

    <!-- FOOTER -->
    <footer class="footer" style="margin-top: 30px;">
        <div class="footer-icons">
            <a href="#"><i class="fa-brands fa-square-facebook" style="color: #0866ff;"></i></a>
            <a href="#"><i class="fa-brands fa-instagram" style="color: #f4109d;"></i></a>
            <a href="#"><i class="fa-brands fa-x-twitter" style="color: #000000;"></i></a>
            <a href="#"><i class="fa-brands fa-linkedin" style="color: #0077b5;"></i></a><br>
            <br>
                <p>Follow us on social media for more updates.</p>
        </div>
        
        <div class="footer-p">
            <p>MediFresh.in</p>
            <p id="tc">Privacy Policy | Terms & Conditions</p> 
            <p><i class="fa-regular fa-copyright"></i>2024 MediFresh. All rights reserved.</p>           
        </div>

        <div class="footer-p">
            <p>Contact Us: +91 1234567890</p>
            <p>Email: support@medimax.com</p>
        </div>

    </footer>

    <script src="index.js"></script>

</body>
</html>