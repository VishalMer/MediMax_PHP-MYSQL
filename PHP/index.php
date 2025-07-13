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
    <title>Home - MediMax.com</title>
</head>
<body>
    <div class="bcimage">
        <img src="../Images/MediMax-BG.jpeg" alt="MediMax Background Image" >
    </div>
    <header class="header">
        <a href="Index.php" class="logo">
            <img src="../Images/MediMax_Logo.png" alt="MediMax">
        </a>

        <div class="search-bar">
            <form method="post" action="">
                <input type="search" name="search_input" placeholder="Search MediFresh.in" id="search-input" value="<?php echo htmlspecialchars($search_query); ?>">
                <button type="submit" name="search" class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
        </div>
        
        <nav class="nav">
           <a href="Index.php" class="active">Home</a>
           <a href="Products.php">Products</a>
           <a href="Orders.php">Orders</a>
           <a href="AboutUs.php">About Us</a>
           <a href="Contact.php">Contact</a>
        </nav>

        <!-- Profile picture or first letter -->
        <div class="profile">
            <button><a href="Wishlist.php"><i class="fa-solid fa-heart" style="color: #ff0000;"></i></a></button>
            <button><a href="Cart.php"><i class="fa-solid fa-cart-plus"></i></a></button>
            <button id="options">
                <div class="pr-pic">
                    <?php if (!empty($user_image)): ?>
                        <img src="../Images/<?php echo htmlspecialchars($user_image); ?>" alt="Profile Picture" style="width: 10px; height: 10px; margin-bottom: 1.5px; border-radius: 50%;">
                    <?php else: ?>
                        <span><?php echo $firstLetter; ?></span>
                    <?php endif; ?>
                </div>
                <div id="userName"><?php echo htmlspecialchars($fetch_user['name']); ?></div>
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
            <a href="index.php?logout=<?php echo $user_id; ?>" 
               onclick="return confirm('Are you sure you want to log out ??');">Log Out <i class="fa-solid fa-right-from-bracket" style="color: #ffffff;"></i></a>
        </button>
    </div>

    <section class="main">
        <?php
        if (isset($message)) {
            foreach ($message as $message) {
                echo '<div class="message" onclick="this.remove();">'.$message.'</div>';
            }
        }
        ?>

        <div class="mainText">
            <h2>Welcome to <span>MediMax</span></h2>
            <p><br>"The MediMax website, with its <br>
                user-friendly interface and streamlined features, offers<br>
                a highly convenient, accessible, and efficient platform for purchasing<br>
                a wide range of essential medicines, healthcare products, and wellness items."</p><br><br>
            <button><a href="AboutUs.php"> Discover More...</a></button>
        </div><br>

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
        <form method="post" action="" class="box">       
            <h2>Product1</h2>
            <div class="box-img" style="background-image: url('../Images/Bandage Roll.jpg')"></div>
            <div class="box-bottom">
                <p>Price: <i class="fa-solid fa-indian-rupee-sign"></i> <strong>499</strong></p>
                <input type="submit" value="Add to cart" name="add_to_cart" id="addToCart">
                <button type="submit" name="add_to_wishlist" id="add-wishlist" class="wishlist-btn">
                    <i class="<?php echo $is_in_wishlist ? 'fa-solid fa-heart' : 'fa-regular fa-heart'; ?>" 
                       style="<?php echo $is_in_wishlist ? 'color: #ff0000;' : ''; ?>"></i>
                </button>
            </div><br>
        </form>
        </div>
    </section>

    <!-- FOOTER -->
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
            <p>MediMax.in</p>
            <p id="tc">Privacy Policy | Terms & Conditions</p> 
            <p><i class="fa-regular fa-copyright"></i>2024 MediMax. All rights reserved.</p>           
        </div>

        <div class="footer-p">
            <p>Contact Us: +91 1234567890</p>
            <p>Email: support@medimax.in</p>
        </div>
    </footer>
    
    <script src="index.js"></script>
</body>
</html>