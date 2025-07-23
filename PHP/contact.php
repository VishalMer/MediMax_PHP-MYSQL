<?php
// PHP/Contact.php

// --- START: Crucial Cache Control Headers for Contact.php ---
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // A date in the past (effective for preventing caching)
// --- END: Crucial Cache Control Headers ---

// Include your database connection FIRST
include 'connection.php'; // connection.php is in the same directory (PHP/)

// Include the centralized user session and details logic.
// This file handles session_start(), fetching user details ($user_id, $username, etc.),
// and processing the `logout` GET parameter. It also initializes and manages the $message array.
include 'user_session.php'; // user_session.php is also in the same directory (PHP/)

// --- Enforce Login for Contact Page ---
// If the user is not logged in ($user_id will be null as set by user_session.php),
// redirect them to the login page with a message.
if ($user_id === null) {
    // Set a message for the login page
    $_SESSION['message'][] = 'Please login to contact us.';
    header('Location: login_form.php'); // Redirect to login form
    exit(); // Stop script execution after redirect
}

// All subsequent operations will now safely use $user_id, $username, $user_email, etc.

// --- Search bar logic for header (if it redirects to products.php) ---
// This part remains specific to this page's header functionality
if (isset($_POST['search'])) {
    $search_input = htmlspecialchars($_POST['search_input']);
    // Redirect to products.php with the search query
    header('Location: products.php?search=' . urlencode($search_input));
    exit(); // Always exit after a header redirect
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Contact - MediMax.com</title>
    <style>
        /* Your existing inline styles can go here if needed */
    </style>
</head>
<body>
    <header class="header">
        <a href="../index.php" class="logo">
            <img src="../Images/MediMax_Logo.png" alt="MediMax">
        </a>

        <div class="search-bar ">
            <form method="post" action="contact.php">
                <input type="search" name="search_input" placeholder="Search MediMax.com" id="search-input">
                <button type="submit" name="search" class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
        </div>

        <nav class="nav">
            <a href="../index.php">Home</a>
            <a href="Products.php">Products</a>
            <a href="Orders.php">Orders</a>
            <a href="AboutUs.php" >About Us</a>
            <a href="Contact.php" class="active">Contact</a>
        </nav>

        <div class="profile">
            <?php if ($user_id !== null): // Check if user is logged in ?>
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
            <?php else: // User is not logged in, show Login/Register button ?>
                <button>
                <a href="login_form.php">Login/Register <i class="fa-solid fa-user-plus"></i></a>
                </button>
            <?php endif; ?>
        </div>
    </header>

    <?php if ($user_id !== null): ?>
    <div class="pr-options hide">
        <button><a href="Update Profile.php">Update User Profile <i class="fa-solid fa-address-card" style="color: #ffffff;"></i></a></button><br>
        <button><a href="Update Password.php">Change Password <i class="fa-solid fa-key" style="color: #ffffff;"></i></a></button><br>

        <?php if ($user_role === 'admin' || $user_role === 'owner') { ?>
        <button><a href="AdminPanel.php" target="_blank">Admin Panel <i class="fa-solid fa-user-tie"></i></a></button><br>
        <?php } ?>

        <button>
            <a href="../index.php?logout=true"  onclick="return confirm('Are you sure you want to log out ??');">Log Out <i class="fa-solid fa-right-from-bracket" style="color: #ffffff;"></i></a>
        </button>
    </div>
    <?php endif; ?>

    <?php
    // The $message variable is now managed by user_session.php via $_SESSION['message']
    if (!empty($_SESSION['message'])) {
        foreach ($_SESSION['message'] as $msg) {
            echo '<div class="cart-msg message" onclick="this.remove();">' . htmlspecialchars($msg) . '</div>';
        }
        unset($_SESSION['message']); // Clear messages after displaying them
    }
    ?>

    <main>
    <div class="container container-contact">
        <div class="headbar">
            <h1 class="head">H<br>E<br>L<br>P<br><br> I<br>M<br>P<br>R<br>O<br>V<br>E<br><br> U<br>S</h1>
        </div>
        <div class="main-content">
            <h1>Your Feedback Matters</h1>
            <p><br> We value your input and are always looking to improve our services.
                Please let us know how we can better serve you by providing feedback,
                reporting issues, or suggesting new features.<br><br></p>

            <form action="submit_feedback.php" method="post" id="contactForm">
                <label for="name">Your Name: <span id="nameError"></span></label>
                <input type="text" id="uname" name="name" value="<?php echo htmlspecialchars($username); ?>" readonly>

                <label for="email">Your Email: <span id="emailError"></span></label>
                <input type="email" id="uemail" value="<?php echo htmlspecialchars($user_email); ?>" name="email" readonly>

                <label for="feedback">Your Feedback: <span id="feedbackError"></span></label>
                <textarea id="ufeedback" name="feedback" rows="5" placeholder="Share your thoughts, suggestions, or report any issues..." required></textarea>
                <input type="submit" value="Submit Feedback">
            </form>
            <script src="../JS/Contact_Validation.js"></script>
            <div class="contact-info">
                <h3>Contact Us Directly On :</h3>
                <p>Email: <a href="mailto:support@medimax.com">support@medimax.com</a></p>
                <p>Phone: +91 1234567890</p>
                <p>Address: 123 E-Commerce , City-Amreli , State-gujrat, 12345</p>
            </div>
        </div>
    </div>
    </main>

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

    <script src="../index.js"></script>
</body>
</html>