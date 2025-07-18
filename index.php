<?php
// index.php

// --- START: Crucial Cache Control Headers for Index.php ---
// These headers tell the browser NOT to cache this page.
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // A date in the past (effective for preventing caching)
// --- END: Crucial Cache Control Headers ---

// Include your database connection FIRST, so $conn is available for all subsequent includes/queries.
include 'PHP/connection.php'; // This should define $conn

// Include the centralized user session and details logic.
// This file will now handle session_start(), fetching user details ($user_id, $username, etc.),
// and processing the `logout` GET parameter. It also initializes and manages the $message array.
include 'PHP/user_session.php';

// Include products_buttons.php for handling add to cart/wishlist actions.
// This file will use the $user_id and $message array prepared by user_session.php.
include 'PHP/products_buttons.php';

// Initialize search query to prevent undefined variable notice
// This remains specific to index.php's display and immediate handling.
$search_query = '';
if (isset($_POST['search'])) {
    $search_query = htmlspecialchars($_POST['search_input']);
    // If you want search to actually redirect to products.php, add that here:
    // header('Location: PHP/Products.php?search=' . urlencode($search_query));
    // exit();
}

// Note: $user_id, $username, $firstLetter, $user_role, $user_image, and $message
// are all now populated by PHP/user_session.php. You no longer need to manually initialize or fetch them here.
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - MediMax.com</title>
    <link rel="stylesheet" href="CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <div class="bcimage">
        <img src="Images/MediMax-BG.jpeg" alt="MediMax Background Image" >
    </div>
    <header class="header">
        <a href="index.php" class="logo">
            <img src="Images/MediMax_Logo.png" alt="MediMax">
        </a>

        <div class="search-bar">
            <form method="post" action="index.php"> <input type="search" name="search_input" placeholder="Search MediMax.com" id="search-input" value="<?php echo htmlspecialchars($search_query); ?>">
                <button type="submit" name="search" class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
        </div>
        
        <nav class="nav">
           <a href="index.php" class="active">Home</a>
           <a href="PHP/Products.php">Products</a>
           <a href="PHP/Orders.php">Orders</a>
           <a href="PHP/AboutUs.php">About Us</a>
           <a href="PHP/Contact.php">Contact</a>
        </nav>

        <div class="profile">
        <?php if ($user_id !== null): ?> <button><a href="PHP/Wishlist.php"><i class="fa-solid fa-heart" style="color: #ff0000;"></i></a></button>
            <button><a href="PHP/cart.php"><i class="fa-solid fa-cart-plus"></i></a></button>
            <button id="options">
                <div class="pr-pic">
                    <?php if (!empty($user_image)): ?>
                        <img src="Images/<?php echo htmlspecialchars($user_image); ?>" alt="Profile Picture" style="width: 10px; height: 10px; margin-bottom: 1.5px; border-radius: 50%;">
                    <?php else: ?>
                        <span><?php echo htmlspecialchars($firstLetter); ?></span>
                    <?php endif; ?>
                </div>
                <div id="userName"><?php echo htmlspecialchars($username); ?></div> </button>
        <?php else: ?>
            <button>
                <a href="PHP/login_form.php">Login/Register</a>
            </button>
        <?php endif; ?>
        </div>
    </header>
    
    <?php if ($user_id !== null): ?> <div class="pr-options hide">
            <button><a href="PHP/Update Profile.php">Update User Profile <i class="fa-solid fa-address-card" style="color: #ffffff;"></i></a></button><br>
            <button><a href="PHP/Update Password.php">Change Password <i class="fa-solid fa-key" style="color: #ffffff;"></i></a></button><br>

            <?php if ($user_role === 'admin' || $user_role === 'owner') { ?>
                <button><a href="PHP/AdminPanel.php" target="_blank">Admin Panel <i class="fa-solid fa-user-tie"></i></a></button><br>
            <?php } ?>

            <button>
                <a href="?logout=true"  onclick="return confirm('Are you sure you want to log out?');">Log Out <i class="fa-solid fa-right-from-bracket" style="color: #ffffff;"></i></a>
            </button>
        </div>
    <?php endif; ?>

    <section class="main">
        <?php
        // Display messages from $_SESSION['message'] (now managed by user_session.php)
        if (!empty($message)) {
            foreach ($message as $msg) {
                echo '<div class="message" onclick="this.remove();">'.$msg.'</div>';
            }
        }
        ?>

        <div class="mainText">
            <h2>Welcome to <span>MediMax</span></h2>
            <p><br>"The MediMax website, with its <br>
                user-friendly interface and streamlined features, offers<br>
                a highly convenient, accessible, and efficient platform for purchasing<br>
                a wide range of essential medicines, healthcare products, and wellness items."</p><br><br>
            <button><a href="PHP/AboutUs.php"> Discover More...</a></button> </div><br>

        <div class="best-sellings">
            <marquee behavior="alternate" loop>
                <i class="fa-solid fa-angles-down"></i>
                Scroll Down For Best Selling Products 
                <i class="fa-solid fa-angles-down"></i>
                Scroll Down For Best Selling Products 
                <i class="fa-solid fa-angles-down"></i>
            </marquee>
        </div>

       <div class="shopping"> 
        <?php
            // Search products based on the search query
            if (!empty($search_query)) {
                $search_sql = mysqli_real_escape_string($conn, $search_query);
                $select_product = mysqli_query($conn, "SELECT * FROM `products` WHERE name LIKE '%$search_sql%'") or die('Query failed: ' . mysqli_error($conn));
            } else {
                $select_product = mysqli_query($conn, "SELECT * FROM `products` LIMIT 5") or die('Query failed: ' . mysqli_error($conn));
            }

            if (mysqli_num_rows($select_product) > 0) {
                while ($fetch_product = mysqli_fetch_assoc($select_product)) {
                    // Initialize $is_in_wishlist
                    $is_in_wishlist = false;
                    // Only check wishlist if user is logged in
                    // $user_id is now provided by user_session.php
                    if ($user_id !== null) {
                        $wishlist_check = mysqli_query($conn, "SELECT * FROM `wishlist` WHERE name = '" . mysqli_real_escape_string($conn, $fetch_product['name']) . "' AND user_id = '$user_id'") or die('Query failed: ' . mysqli_error($conn));
                        $is_in_wishlist = mysqli_num_rows($wishlist_check) > 0;
                    }
            ?>
                    <form method="post" class="box" action="index.php"> <h2><?php echo htmlspecialchars($fetch_product['name']); ?></h2>
                        <div class="box-img" style="background-image: url('Images/<?php echo htmlspecialchars($fetch_product['image']); ?>')"></div>
                        <div class="box-bottom">
                            <p>Price: <i class="fa-solid fa-indian-rupee-sign"></i> <strong><?php echo htmlspecialchars($fetch_product['price']); ?></strong></p>
                            <?php if ($user_id !== null): ?> <button type="submit" name="add_to_cart" id="addToCart"><i class="fa-solid fa-cart-plus"></i></button>
                                <button type="submit" name="add_to_wishlist" id="add-wishlist" class="wishlist-btn">
                                    <i class="<?php echo $is_in_wishlist ? 'fa-solid fa-heart' : 'fa-regular fa-heart'; ?>" 
                                       style="<?php echo $is_in_wishlist ? 'color: #ff0000;' : ''; ?>"></i>
                                </button>
                            <?php else: ?>
                                <button type="button" onclick="window.location.href='PHP/login_form.php';" title="Login to add to cart"><i class="fa-solid fa-cart-plus"></i></button>
                                <button type="button" onclick="window.location.href='PHP/login_form.php';" title="Login to add to wishlist"><i class="fa-regular fa-heart"></i></button>
                            <?php endif; ?>
                        </div><br>

                        <input type="hidden" name="product_image" value="<?php echo htmlspecialchars($fetch_product['image']); ?>">
                        <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($fetch_product['name']); ?>">
                        <input type="hidden" name="product_price" value="<?php echo htmlspecialchars($fetch_product['price']); ?>">
                    </form>
            <?php
                }
            } else {
                echo "<p class='no-products'>No products found, We will make it available as soon as possible..</p>";
            }
            ?>
        </div>
    </section>

    <footer class="footer">
        <div class="footer-icons">
            <a href="#"><i class="fa-brands fa-square-facebook" style="color: #0866ff;"></i></a>
            <a href="#"><i class="fa-brands fa-instagram" style="color: #f4109d;"></i></a>
            <a href="#"><i class="fa-brands fa-x-twitter" style="color: #000000;"></i></a>
            <a href="#"><i class="fa-brands fa-linkedin" style="color: #0077b5;"></i></a><br>
            <br>
            <p>Follow us on social media for more updates.</p>
        </div>

        <div class="footer-p">
            <p>MediMax.com</p>
            <p id="tc">Privacy Policy | Terms & Conditions</p> 
            <p><i class="fa-regular fa-copyright"></i>2024 MediMax. All rights reserved.</p>         
        </div>

        <div class="footer-p">
            <p>Contact Us: +91 1234567890</p>
            <p>Email: support@medimax.com</p>
        </div>
    </footer>
    
    <script src="index.js"></script>
</body>
</html>