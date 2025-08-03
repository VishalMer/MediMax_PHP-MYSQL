<?php

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

include 'connection.php'; 
include 'user_session.php'; 

// --- Search bar logic
if (isset($_POST['search'])) {
    $search_input = htmlspecialchars($_POST['search_input']);
    
    header('Location: products.php?search=' . urlencode($search_input));
    exit(); 
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>About Us - MediMax.com</title>
</head>
<body bgcolor="#a6cbd3">
    <div class="bcimage">
        <img src="../Images/MediMax-BG.jpeg" alt="MediMax Background">
    </div>
    <header class="header">
        <a href="../index.php" class="logo">
            <img src="../Images/MediMax_Logo.png" alt="MediMax">
        </a>

        <div class="search-bar ">
            <form method="post" action="aboutUs.php">
                <input type="search" name="search_input" placeholder="Search MediMax.com" id="search-input">
                <button type="submit" name="search" class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
        </div>

        <nav class="nav">
            <a href="../index.php">Home</a>
            <a href="Products.php">Products</a>
            <a href="Orders.php">Orders</a>
            <a href="AboutUs.php" class="active">About Us</a>
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
            <a href="admin/update_profile.php"><button>Update User Profile <i class="fa-solid fa-address-card" style="color: #ffffff;"></i></button></a><br>
            <a href="admin/update_password.php"><button>Change Password <i class="fa-solid fa-key" style="color: #ffffff;"></i></button></a><br>

            <?php if ($user_role === 'admin' || $user_role === 'owner') { ?>
                <a href="admin/admin_panel.php" target="_blank"><button>Admin Panel <i class="fa-solid fa-user-tie"></i></button></a><br>
            <?php } ?>

            <a href="contact.php"><button>Support <i class="fa-solid fa-headset" style="color: #ffffff;"></i></button></a><br>
            
            <a href="?logout=true"  onclick="return confirm('Are you sure you want to log out?');">
                <button>Log Out <i class="fa-solid fa-right-from-bracket" style="color: #ffffff;"></i></button>
            </a>
            
        </div>
    <?php endif; ?>

    <?php

    if (!empty($_SESSION['message'])) {
        foreach ($_SESSION['message'] as $msg) {
            echo '<div class="cart-msg message" onclick="this.remove();">' . htmlspecialchars($msg) . '</div>';
        }
        unset($_SESSION['message']); 
    }
    ?>

    <div class="section">
        <div class="container">
            <div class="content-section">
                <div class="title">
                    <p>About Us</p>
                </div>
                <div class="content">
                    <h3><i class="fa-solid fa-arrows-to-dot"></i> Welcome to the MediMax website.</h3>
                    <p>The MediMax website is a new web application designed to help users manage their
                    Health efficiently.</p>
                    <p>The MediMax website features a user-friendly interface and streamlined features,
                    making it easy to purchase essential medicines, healthcare products, and wellness items.</p>
                    <p>MediMax provides 24/7 access to medications, competitive pricing, and
                    telemedicine services, serving remote areas with rapid growth.</p><br>
                </div>
            </div>
            <div class="image-section">
                <img src="../Images/Medical.gif" alt="About us" width="380px">
            </div>
        </div>
        <div class="show-more hide">
            <h3><i class="fa-solid fa-arrows-to-dot"></i> MediMax offers a convenient online shopping experience with:</h3>
            <ul>
                <li><p>User-friendly website for easy navigation</p></li>
                <li><p>Hassle-free return policy for products that don't meet expectations</p></li>
                <li><p>Secure payment options (credit card & online banking) for safe transactions</p></li>
                <li><p>Fast delivery (orders dispatched within 24 hours and delivered within 3-5 days)</p></li>
            </ul><br>
            <h3><i class="fa-solid fa-arrows-to-dot"></i> MediMax offers a wide range of products and services, including:</h3>
            <ul>
                <li><p>Competitive pricing for affordable options</p></li>
                <li><p>Authentic and genuine products from reputable sources</p></li>
                <li><p>Dedicated customer support for any queries or concerns</p></li>
                <li><p>A wide range of medicines and healthcare products (prescription, OTC, and supplements)</p></li>
            </ul><br>
            <h3><i class="fa-solid fa-arrows-to-dot"></i> Expert Advice and Support</h3>
            <p>Our team of medical experts is available to provide guidance and support throughout your shopping experience.
                Whether you need help finding the right medicine or have questions about your order,
                our customer support team is here to assist you.</p><br>
            <h3><i class="fa-solid fa-arrows-to-dot"></i> Certified Partner with Reputable Suppliers</h3>
            <p>MediMax is a certified partner with reputable suppliers, ensuring that our products meet
                the highest standards of quality and authenticity. We work closely with our suppliers to ensure that
                our products are sourced from reliable manufacturers and stored in controlled environments.</p><br>
            <h3><i class="fa-solid fa-arrows-to-dot"></i> Customer Reviews and Ratings</h3>
            <p>MediMax values customer feedback and encourages reviews and ratings from our customers.
                This helps us to improve our services and products, ensuring that we continue to meet the needs of our customers.</p><br>
        </div>
        <button id="read-more">Read more</button>
    </div>

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

    <script src="../JS/index.js"></script> 
</body>
</html>