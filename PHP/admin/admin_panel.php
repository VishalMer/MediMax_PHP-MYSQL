<?php
include '../connection.php';
include 'admin_details.php'; // This file now sets $loggedInUserRole, $username, $firstLetter, $image, $user_id

// Log out logic
// Ensure session_start() is called once in admin_details.php or connection.php
if (isset($_GET['logout'])) {
    // Unset all of the session variables
    $_SESSION = array();

    // Destroy the session
    session_destroy();

    // Redirect to login form
    header('Location: ../LoginForm.php'); // Corrected path to LoginForm.php
    exit();
}

// Initialize variables to 0 or appropriate defaults in case queries fail
$orderCount = 0;
$customerCount = 0;
$totalTurnover = 0;

// Fetch dashboard statistics using prepared statements for security
// --- Total Orders ---
$stmt_orders = $conn->prepare("SELECT COUNT(*) FROM `orders` WHERE `quantity` > 0");
if ($stmt_orders) {
    $stmt_orders->execute();
    $stmt_orders->bind_result($orderCount);
    $stmt_orders->fetch();
    $stmt_orders->close();
} else {
    // Log error or handle gracefully
    error_log("Failed to prepare orderCount statement: " . $conn->error);
}

// --- Total Customers ---
$stmt_customers = $conn->prepare("SELECT COUNT(*) FROM `users`");
if ($stmt_customers) {
    $stmt_customers->execute();
    $stmt_customers->bind_result($customerCount);
    $stmt_customers->fetch();
    $stmt_customers->close();
} else {
    // Log error or handle gracefully
    error_log("Failed to prepare customerCount statement: " . $conn->error);
}

// --- Total Turnover ---
$stmt_turnover = $conn->prepare("SELECT SUM(price * quantity) AS total FROM `orders` WHERE payment = 'Completed'"); // Only sum completed payments
if ($stmt_turnover) {
    $stmt_turnover->execute();
    $stmt_turnover->bind_result($totalTurnover_raw);
    $stmt_turnover->fetch();
    $stmt_turnover->close();
    $totalTurnover = $totalTurnover_raw ? $totalTurnover_raw : 0; // Ensure it's zero if NULL
} else {
    // Log error or handle gracefully
    error_log("Failed to prepare totalTurnover statement: " . $conn->error);
}

// No $message array in this file, so no message display block needed
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../CSS/adminPanel.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Admin Panel - MediMax.com</title>
    <style>
        /* Your existing styles here, or in adminPanel.css */
    </style>
</head>
    <body style="background-image: url('../../Images/MediMax-BG.jpeg');">
    
    <div class="header">
        <a href="admin_panel.php" > <div class="user-info">
                <div class="profile-pic">
                    <?php if (!empty($image)): ?>
                        <img src="../../Images/<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($username); ?>" style="width: 50px; height: 50px; border-radius: 50%;">
                    <?php else: ?>
                        <span class="initials"><?php echo htmlspecialchars($firstLetter ?? '');?></span>
                    <?php endif; ?>
                </div>
                <div class="user-name">
                    <span><?php echo htmlspecialchars($username ?? 'Guest');?></span><br>
                    <span class="user-bio">You're <?php echo htmlspecialchars($loggedInUserRole ?? '');?>.</span>
                </div>
            </div>
        </a>

        <nav class="nav">
           <a href="admin_panel.php" class="active">Dashboard</a>
           <a href="product_list.php">Products</a>
           <a href="add_product.php">Add Product</a>
           <a href="all_orders.php">Orders</a>
           <a href="users.php">Users</a>
           <a href="add_user.php">Add Users</a>
           <a href="../../index.php" target="_blank">Go To Web</a>
           <a href="admin_panel.php?logout=<?php echo htmlspecialchars($user_id ?? ''); ?>" 
                onclick="return confirm('Are you sure you want to logout ??');">LogOut
                <i class="fa-solid fa-right-from-bracket" style="color: #ffffff;"></i>
           </a>
        </nav>
    </div>

    <div class="section">
        <h1>Admin Panel</h1>
        <p>Welcome to the Admin Panel of MediMax.com</p>

        <h2>Overview</h2><br>
        <div class="stats">
            <div class="stat">
                <h3>Total Orders</h3>
                <p><?php echo htmlspecialchars($orderCount); ?> <i class="fa-solid fa-cart-arrow-down"></i></p>
            </div>
            <div class="stat">
                <h3>Total Customers</h3>
                <p><?php echo htmlspecialchars($customerCount); ?> <i class="fa-solid fa-user-plus"></i></p>
            </div>
            <div class="stat">
                <h3>Total Turnover</h3>
                <p><?php echo number_format($totalTurnover, 2); ?> INR</p> 
            </div>
            
        </div>

        <h2>Services</h2><br>
        <div class="stats">

            <a href="update_profile.php"> <div class="stat">
                    <h3>Update Profile</h3>
                    <p><i class="fa-solid fa-address-card"></i></p>
                </div>
            </a>

            <a href="product_list.php">
                <div class="stat">
                    <h3>Products List</h3>
                    <p><i class="fa-solid fa-list-ul"></i></p>
                </div>
            </a>

            <a href="add_product.php">
                <div class="stat">
                    <h3>Add Products</h3>
                    <p><i class="fa-solid fa-circle-plus"></i></p>
                </div>
            </a>
            
            <a href="all_orders.php">
                <div class="stat">
                    <h3>Orders</h3>
                    <p><i class="fa-solid fa-cart-arrow-down"></i></p>
                </div>
            </a>
            
            <a href="users.php">
                <div class="stat">
                    <h3>Users List</h3>
                    <p><i class="fa-solid fa-address-book"></i></p>
                </div>
            </a>

            <a href="add_user.php">
                <div class="stat">
                    <h3>Add Users</h3>
                    <p><i class="fa-solid fa-user-plus"></i></p>
                </div>
            </a>

            <a href="../../index.php" target="_blank"> 
                <div class="stat">
                    <h3>Go To Website</h3>
                    <p><i class="fa-solid fa-diamond-turn-right"></i></p>
                </div>
            </a>

            <a href="admin_panel.php?logout=<?php echo htmlspecialchars($user_id ?? ''); ?>" onclick="return confirm('Are you sure you want to logout ??');"> 
                <div class="stat">
                    <h3>Log Out</h3>
                    <p><i class="fa-solid fa-right-from-bracket"></i></p>
                </div>
            </a>

        </div>

    </div>

    <script src="../../JS/admin.js"></script>

</body>
</html>