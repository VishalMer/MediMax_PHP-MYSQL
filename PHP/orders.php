<?php

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); 

include 'connection.php'; 
include 'user_session.php'; 

if ($user_id === null) {

    $_SESSION['message'][] = 'Please login to view your orders.';
    header('Location: login_form.php'); 
    exit();
}

$escaped_user_id = mysqli_real_escape_string($conn, $user_id);
$orders_query = mysqli_query($conn, "SELECT * FROM `orders` WHERE user_id = '$escaped_user_id'") or die('Query failed: ' . mysqli_error($conn));

$total_products = mysqli_num_rows($orders_query);
$total_amount = 0;
$paid_amount = 0;    
$remaining_amount = 0; 

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
    <link rel="stylesheet" href="../CSS/style.css"> <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Orders - MediMax.com</title> </head>
<body>
    <header class="header">
        <a href="../index.php" class="logo"> <img src="../Images/MediMax_Logo.png" alt="MediMax"> </a>

        <div class="search-bar ">
            <form method="post" action="orders.php"> <input type="search" name="search_input" placeholder="Search MediMax.com" id="search-input"> <button type="submit" name="search" class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
        </div>
        
        <nav class="nav">
           <a href="../index.php">Home</a> <a href="Products.php">Products</a>
           <a href="Orders.php" class="active">Orders</a>
           <a href="AboutUs.php">About Us</a>
           <a href="Contact.php">Contact</a>
        </nav>

        <div class="profile">
                <a href="Wishlist.php"><button><i class="fa-solid fa-heart" style="color: #ff0000;"></i></button></a>
                <a href="Cart.php"><button><i class="fa-solid fa-cart-plus"></i></button></a>
            <button id="options">
                <div class="pr-pic">
                    <?php if (!empty($user_image)): ?>
                        <img src="../Images/<?php echo htmlspecialchars($user_image); ?>" alt="Profile Picture" style="width: 10px; height: 10px; margin-bottom: 1.5px; border-radius: 50%;"> <?php else: ?>
                        <span><?php echo htmlspecialchars($firstLetter); ?></span>
                    <?php endif; ?>
                </div>
                <div id="userName"><?php echo htmlspecialchars($username); ?></div> </button>
        </div>
    </header>

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

    <section class="main">
        <div class="orders">
            <table>
                <tr>
                    <th>Order ID</th>
                    <th>Product</th>
                    <th>Image</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Sub Total</th>
                    <th>Payment</th>
                    <th>Delivery</th>
                </tr>
                
                <?php
                // Check if there are any orders and display them
                if($total_products > 0) {
                    
                    while ($order = mysqli_fetch_assoc($orders_query)) {
                        $sub_total = $order['price'] * $order['quantity'];
                        $total_amount += $sub_total; 

                        // Add to paid amount if payment is completed
                        if ($order['payment'] === 'Completed') {
                            $paid_amount += $sub_total;
                        }
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['id']); ?></td>
                            <td><?php echo htmlspecialchars($order['name']); ?></td>
                            <td><img src="../Images/<?php echo htmlspecialchars($order['image']); ?>" alt="<?php echo htmlspecialchars($order['name']); ?>" style="width: 50px; height: 50px;"></td> <td><?php echo htmlspecialchars($order['quantity']); ?></td>
                            <td>₹ <?php echo htmlspecialchars($order['price']); ?></td>
                            <td>₹ <?php echo htmlspecialchars($sub_total); ?></td>
                            <td><?php echo htmlspecialchars($order['payment']); ?></td>
                            <td><?php echo htmlspecialchars($order['delevery']); ?></td>
                        </tr>
                        <?php
                    }
                } else {
                    echo "<tr><td colspan='8' class='no-orders-found'><h1>No orders found</h1></td></tr>"; 
                }

                // Calculate remaining amount
                $remaining_amount = $total_amount - $paid_amount; 
                ?>
                
                <tr>
                    <th colspan="2">Total Products: <?php echo htmlspecialchars($total_products); ?></th>
                    <th colspan="2">Total Amount: ₹ <?php echo htmlspecialchars(number_format($total_amount, 2)); ?></th>
                    <th colspan="2">Paid Amount: ₹ <?php echo htmlspecialchars(number_format($paid_amount, 2)); ?></th>
                    <th colspan="2">Remaining Amount: ₹ <?php echo htmlspecialchars(number_format($remaining_amount, 2)); ?></th>
                </tr>
            </table>
        </div>    
    </section>

    <footer class="footer" style="margin-top: auto;">
        <div class="footer-icons">
            <a href="#"><i class="fa-brands fa-square-facebook" style="color: #0866ff;"></i></a>
            <a href="#"><i class="fa-brands fa-instagram" style="color: #f4109d;"></i></a>
            <a href="#"><i class="fa-brands fa-x-twitter" style="color: #000000;"></i></a>
            <a href="#"><i class="fa-brands fa-linkedin" style="color: #0077b5;"></i></a><br>
            <br>
            <p>Follow us on social media for more updates.</p>
        </div>
        
        <div class="footer-p">
            <p>MediMax.com</p> <p id="tc">Privacy Policy | Terms & Conditions</p> 
            <p><i class="fa-regular fa-copyright"></i>2024 MediMax. All rights reserved.</p>           
        </div>

        <div class="footer-p">
            <p>Contact Us: +91 1234567890</p>
            <p>Email: support@medimax.com</p> </div>
    </footer>

    <script src="../JS/index.js"></script>
</body>
</html>