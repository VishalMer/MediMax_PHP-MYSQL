<?php
// Cache Control Headers
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Fri, 06 May 2005 05:00:00 GMT");

include 'PHP/connection.php';
include 'PHP/user_session.php'; 
include 'PHP/products_buttons.php';

$search_query = '';
if (isset($_POST['search'])) {
    $search_query = htmlspecialchars($_POST['search_input']);
    //redirect to products page on searching
    header('Location: PHP/Products.php?search=' . urlencode($search_query));
    exit();
}
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
        <?php if ($user_id !== null): ?>
                <a href="PHP/Wishlist.php"><button><i class="fa-solid fa-heart" style="color: #ff0000;"></i></button></a>
                <a href="PHP/cart.php"><button><i class="fa-solid fa-cart-plus"></i></button></a>
            <button id="options">
                <div class="pr-pic">
                    <?php if (!empty($user_image)): ?>
                        <img src="Images/<?php echo htmlspecialchars($user_image); ?>" alt="Profile Picture" style="width: 10px; height: 10px; margin-bottom: 1.5px; border-radius: 50%;">
                    <?php else: ?>
                        <span><?php echo htmlspecialchars($firstLetter); ?></span>
                    <?php endif; ?>
                </div>
                <div id="userName"><?php echo htmlspecialchars($username); ?></div> 
            </button>
        <?php else: ?>
            <a href="PHP/login_form.php">
                <button>Login/Register <i class="fa-solid fa-user-plus"></i></button>
            </a>
        <?php endif; ?>
        </div>
    </header>
    
    <?php if ($user_id !== null): ?> <div class="pr-options hide">
            <a href="PHP/admin/update_profile.php"><button>Update User Profile <i class="fa-solid fa-address-card" style="color: #ffffff;"></i></button></a><br>
            <a href="PHP/admin/update_password.php"><button>Change Password <i class="fa-solid fa-key" style="color: #ffffff;"></i></button></a><br>

            <?php if ($user_role === 'admin' || $user_role === 'owner') { ?>
                <a href="PHP/admin/admin_panel.php" target="_blank"><button>Admin Panel <i class="fa-solid fa-user-tie"></i></button></a><br>
            <?php } ?>

            <a href="PHP/contact.php"><button>Support <i class="fa-solid fa-headset" style="color: #ffffff;"></i></button></a><br>
            
            <a href="?logout=true"  onclick="return confirm('Are you sure you want to log out?');">
                <button>Log Out <i class="fa-solid fa-right-from-bracket" style="color: #ffffff;"></i></button>
            </a>
            
        </div>
    <?php endif; ?>

    <section class="main">
        <?php
        if (isset($_SESSION['message']) && !empty($_SESSION['message'])) {
            foreach ($_SESSION['message'] as $msg) {
                echo '<div class="message" onclick="this.remove();">' . htmlspecialchars($msg) . '</div>';
            }
            unset($_SESSION['message']); 
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
        //fetch cart and wishlist for user
            $user_cart_items = [];
            $user_wishlist_items = [];
            if ($user_id !== null) {
                $cart_res = mysqli_query($conn, "SELECT name FROM `cart` WHERE user_id = '$user_id'");
                while ($row = mysqli_fetch_assoc($cart_res)) {
                    $user_cart_items[] = $row['name'];
                }
                $wishlist_res = mysqli_query($conn, "SELECT name FROM `wishlist` WHERE user_id = '$user_id'");
                while ($row = mysqli_fetch_assoc($wishlist_res)) {
                    $user_wishlist_items[] = $row['name'];
                }
            }

            // Search products 
            if (!empty($search_query)) {
                $search_sql = mysqli_real_escape_string($conn, $search_query);
                $select_product = mysqli_query($conn, "SELECT * FROM `products` WHERE name LIKE '%$search_sql%'") or die('Query failed: ' . mysqli_error($conn));
            } else {
                $select_product = mysqli_query($conn, "SELECT * FROM `products` ORDER BY RAND() LIMIT 5") or die('Query failed: ' . mysqli_error($conn));
            }

            if (mysqli_num_rows($select_product) > 0) {
                while ($fetch_product = mysqli_fetch_assoc($select_product)) {
                    $is_in_wishlist = false;
                    // check wishlist if user is logged in
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
                                    <button type="submit" name="add_to_cart" title="Login to add to cart"><i class="fa-solid fa-cart-plus"></i></button>
                                    <button type="submit" name="add_to_wishlist" title="Login to add to wishlist"><i class="fa-regular fa-heart"></i></button>
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
            <p><i class="fa-regular fa-copyright"></i>2025 MediMax. All rights reserved.</p>         
        </div>

        <div class="footer-p">
            <p>Contact Us: +91 1234567890</p>
            <p>Email: support@medimax.com</p>
        </div>
    </footer>
    
    <script src="JS/index.js"></script>
</body>
</html>