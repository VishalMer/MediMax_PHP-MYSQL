<?php

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

include 'connection.php';
include 'user_session.php';
include 'products_buttons.php';

$search_query = '';
if (isset($_POST['search'])) {
    $search_query = htmlspecialchars($_POST['search_input']);
    header('Location: ' . $_SERVER['PHP_SELF'] . '?search=' . urlencode($search_query));
    exit();
} else if (isset($_GET['search'])) {
    $search_query = htmlspecialchars($_GET['search']);
}

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
<body style="min-height: 100vh;">

    <header class="header">
        <a href="../index.php" class="logo"> <img src="../Images/MediMax_Logo.png" alt="MediMax"> </a>

        <div class="search-bar">
            <form method="post" action="products.php"> 
                <input type="search" placeholder="Search MediMax.com" name="search_input" id="search-input" value="<?php echo htmlspecialchars($search_query); ?>">
                <button type="submit" name="search" class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
        </div>
        
        <nav class="nav">
           <a href="../index.php">Home</a> <a href="products.php" class="active">Products</a>
           <a href="Orders.php">Orders</a>
           <a href="AboutUs.php">About Us</a>
           <a href="Contact.php">Contact</a>
        </nav>

        <div class="profile">
            <?php if ($user_id !== null): ?> 
                <a href="Wishlist.php"><button><i class="fa-solid fa-heart" style="color: #ff0000;"></i></button></a>
                <a href="Cart.php"><button><i class="fa-solid fa-cart-plus"></i></button></a>
                <button id="options">
                    <div class="pr-pic">
                        <?php if (!empty($user_image)): ?>
                            <img src="../Images/<?php echo htmlspecialchars($user_image); ?>" alt="Profile Picture" style="width: 10px; height: 10px; margin-bottom: 1.5px; border-radius: 50%;"> 
                        <?php else: ?>
                            <span><?php echo htmlspecialchars($firstLetter); ?></span>
                        <?php endif; ?>
                    </div>
                    <div id="userName"><?php echo htmlspecialchars($username); ?></div> 
                </button>
            <?php else: ?>
                <a href="login_form.php">
                    <button>Login/Register <i class="fa-solid fa-user-plus"></i></button>
                </a>
            <?php endif; ?>
        </div>
    </header>
        
    <?php if ($user_id !== null): ?> 
    <div class="pr-options hide">
        <a href="Update Profile.php"><button>Update User Profile <i class="fa-solid fa-address-card" style="color: #ffffff;"></i></button></a><br>
        <a href="Update Password.php"><button>Change Password <i class="fa-solid fa-key" style="color: #ffffff;"></i></button></a><br>
        
        <?php if ($user_role === 'admin' || $user_role === 'owner') { ?>
        <a href="AdminPanel.php" target="_blank"><button>Admin Panel <i class="fa-solid fa-user-tie"></i></button></a><br>
        <?php } ?>

        <a href="contact.php"><button>Support <i class="fa-solid fa-headset" style="color: #ffffff;"></i></button></a><br>
        
        <a href="../index.php?logout=true"  onclick="return confirm('Are you sure you want to log out ??');">
            <button>Log Out <i class="fa-solid fa-right-from-bracket" style="color: #ffffff;"></button></i>
        </a>
        
    </div>
    <?php endif; ?>

    <br><br><br><br><br>
        
<?php
if (isset($_SESSION['message']) && !empty($_SESSION['message'])) {
    foreach ($_SESSION['message'] as $msg) {
        echo '<div class="message" onclick="this.remove();">' . htmlspecialchars($msg) . '</div>';
    }
    unset($_SESSION['message']); 
}
?>

<div class="shopping">
    <?php
    
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

        // Search products query
        if (!empty($search_query)) {
            $search_sql = mysqli_real_escape_string($conn, $search_query);
            $select_product = mysqli_query($conn, "SELECT * FROM `products` WHERE name LIKE '%$search_sql%'") or die('Query failed: ' . mysqli_error($conn));
        } else {
            $select_product = mysqli_query($conn, "SELECT * FROM `products`") or die('Query failed: ' . mysqli_error($conn));
        }

        if (mysqli_num_rows($select_product) > 0) {
            while ($fetch_product = mysqli_fetch_assoc($select_product)) {
                $product_name = $fetch_product['name'];
                $is_in_wishlist = in_array($product_name, $user_wishlist_items);
                $is_in_cart = in_array($product_name, $user_cart_items); 
    ?>
                    <form method="post" class="box" action="products.php"> 
                        <h2><?php echo htmlspecialchars($product_name); ?></h2>
                        <div class="box-img" style="background-image: url('../Images/<?php echo htmlspecialchars($fetch_product['image']); ?>')"></div> 
                        <div class="box-bottom">
                            <p>Price: <i class="fa-solid fa-indian-rupee-sign"></i> <strong><?php echo htmlspecialchars($fetch_product['price']); ?></strong></p>
                            <?php if ($user_id !== null): ?> 
                                <button type="submit" name="add_to_cart" id="addToCart" class="cart-btn">
                                    <i class="fa-solid fa-cart-plus"></i>
                                </button>
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
                echo "<p class='no-products'>No products found. We will make them available as soon as possible.</p>";
            }
            ?>
</div>

    <footer class="footer" >
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

    <script src="../JS/index.js"></script> 
</body>
</html>