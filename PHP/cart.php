<?php

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); 

include 'connection.php'; 
include 'user_session.php'; 

if ($user_id === null) {

    $_SESSION['message'][] = 'Please login to view your cart.';
    header('Location: login_form.php');
    exit();
}

// Update cart information
if (isset($_POST['update_cart'])) {
    $update_quantity = mysqli_real_escape_string($conn, $_POST['cart_quantity']);
    $update_id = mysqli_real_escape_string($conn, $_POST['cart_id']);

    if (!is_numeric($update_quantity) || $update_quantity <= 0) {
        $_SESSION['message'][] = 'Invalid quantity provided!';
    } else {
        mysqli_query($conn, "UPDATE `cart` SET quantity = '$update_quantity' WHERE id = '$update_id' AND user_id = '$user_id'") or die('Query failed: ' . mysqli_error($conn));
        $_SESSION['message'][] = 'Cart quantity updated!'; 
    }
}

// Delete item from cart
elseif (isset($_GET['remove'])) {
    $remove_id = mysqli_real_escape_string($conn, $_GET['remove']);
    mysqli_query($conn, "DELETE FROM `cart` WHERE id = '$remove_id' AND user_id = '$user_id'") or die('Query failed: ' . mysqli_error($conn));
    $_SESSION['message'][] = 'Cart item deleted!'; 

    header('Location: cart.php');
    exit();
}

// Delete all items from cart
elseif (isset($_GET['delete_all'])) {
    mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('Query failed: ' . mysqli_error($conn));
    $_SESSION['message'][] = 'All items deleted from cart!'; 

    header('Location: cart.php');
    exit();
}

// Buy All Now - Functionality
if (isset($_POST['buy_all'])) {
    // Fetch all products from the cart
    $cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('Query failed: ' . mysqli_error($conn));
    
    if (mysqli_num_rows($cart_query) > 0) {
        mysqli_begin_transaction($conn);
        $success = true;

        while ($fetch_cart = mysqli_fetch_assoc($cart_query)) {
            $name = mysqli_real_escape_string($conn, $fetch_cart['name']);
            $price = mysqli_real_escape_string($conn, $fetch_cart['price']);
            $image = mysqli_real_escape_string($conn, $fetch_cart['image']);
            $quantity = mysqli_real_escape_string($conn, $fetch_cart['quantity']);            
            $placed_on = date('Y-m-d H:i:s'); 

            $insert_order = mysqli_query($conn, "INSERT INTO `orders` (user_id, name, price, image, quantity, placed_on) VALUES ('$user_id', '$name', '$price', '$image', '$quantity', '$placed_on')");
            
            if (!$insert_order) {
                $success = false;
                $_SESSION['message'][] = 'Failed to place order for ' . htmlspecialchars($name) . ': ' . mysqli_error($conn);
                break; 
            }
        }

        if ($success) {
            // Clear the cart after buy all
            $clear_cart = mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'");
            if ($clear_cart) {
                mysqli_commit($conn); 
                $_SESSION['message'][] = 'All products purchased successfully! Check your orders.';
                header('Location: orders.php'); 
                exit();
            } else {
                mysqli_rollback($conn); // Rollback if clearing cart fails
                $_SESSION['message'][] = 'Failed to clear cart after purchase, but orders were placed.';
            }
        } else {
            mysqli_rollback($conn); // Rollback if any order insertion failed
            $_SESSION['message'][] = 'Error purchasing products. Some orders might not have been placed. Please check your orders page.';
        }

    } else {
        $_SESSION['message'][] = 'Your cart is empty! Add some products first.';
    }
}

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
    <title>Cart - MediMax.com</title>
    <link rel="stylesheet" href="../CSS/style.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    
    <div class="bcimage">
        <img src="../Images/MediMax-BG.jpeg" alt="MediMax Background Image" > 
    </div>
    <header class="header">
        <a href="../index.php" class="logo"> <img src="../Images/MediMax_Logo.png" alt="MediMax"> </a>

        <div class="search-bar ">
            <form method="post" action="cart.php"> 
                <input type="search" name="search_input" placeholder="Search MediMax.com" id="search-input">
                <button type="submit" name="search" class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
        </div>
        
        <nav class="nav">
           <a href="../index.php">Home</a> 
           <a href="products.php">Products</a>
           <a href="orders.php">Orders</a>
           <a href="aboutus.php">About Us</a>
           <a href="contact.php">Contact</a>
        </nav>

        <div class="profile">
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

    <main class="cart">

    <?php
    if(!empty($_SESSION['message'])){ 
        foreach($_SESSION['message'] as $msg){ 
            echo '<div class="cart-msg message" onclick="this.remove();">'.htmlspecialchars($msg).'</div>'; 
        }
        unset($_SESSION['message']);
    }
    ?>

    <table class="cart-container">
                <tr class="cart-header">
                    <th class="cart-title" colspan="4">Your Cart </th>
                </tr>
                <tr>
                    <td class="cart-content">
                        <div class="cart-boxes">
                            <?php
                                $grand_total = 0;
                                $cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die("query failed: " . mysqli_error($conn));
                                if (mysqli_num_rows($cart_query) > 0) {
                                    while ($fetch_cart = mysqli_fetch_assoc($cart_query)) {
                                        $sub_total = $fetch_cart['price'] * $fetch_cart['quantity'];
                                        $grand_total += $sub_total;
                                ?>
                                <div class="cart-box">
                                    <img src="../Images/<?php echo htmlspecialchars($fetch_cart['image']); ?>" alt="<?php echo htmlspecialchars($fetch_cart['name']); ?>"> 
                                    <div class="product-detail">
                                        <h2><?php echo htmlspecialchars($fetch_cart['name']); ?></h2><br>
                                        <pre>Price :             Sub Total : <br><span><i class="fa-solid fa-indian-rupee-sign"></i><?php echo htmlspecialchars(number_format($fetch_cart['price'], 2)); ?>           <i class="fa-solid fa-indian-rupee-sign"></i><?php echo htmlspecialchars(number_format($sub_total, 2));?></span></pre><br>

                                        <p>Quantity :</p>
                                        <div class="cart-quantity">
                                            <form method="post" action="cart.php"> 
                                                <input type="hidden" name="cart_id" value="<?php echo htmlspecialchars($fetch_cart['id']); ?>">
                                                <input type="number" min="1" max="99" name="cart_quantity" value="<?php echo htmlspecialchars($fetch_cart['quantity']); ?>" id="quantity">
                                                <input type="submit" name="update_cart" value="Update" class="updatebtn">
                                            </form>
                                            <a href="Cart.php?remove=<?php echo htmlspecialchars($fetch_cart['id']); ?>"
                                            onclick="return confirm('Remove item from cart?');"><i class="remove-item fa-regular fa-trash-can" style="color: #094e7e;"></i></a>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                    }
                                } else {
                                ?>
                                    <div class="empty-cart">
                                        <p>Your cart is empty</p>
                                        <a href='products.php'><img src="../Images/Empty Cart.GIF" alt="Empty Cart"></a><br> 
                                        <button><a href='products.php'>Continue Shopping</a></button>
                                    </div>
                                <?php
                                }
                                ?>
                        </div>
                    </td>
                </tr>
            
                <tr class="cart-bottom">
                    <td class="cart-options">
                        <div class="cart-footer">
                            <button><a href="products.php">Continue Shopping...</a></button>
                            <form method="post" action="cart.php"> 
                                <button type="submit" name="buy_all" class="<?php echo ($grand_total > 0 && $user_id !== null) ? '' : 'disabled'; ?>">Buy All Now</button>
                            </form>
                            <button class="<?php echo ($grand_total > 0 && $user_id !== null) ? '' : 'disabled'; ?>">
                                <a href="Cart.php?delete_all" onclick="return confirm('Are you sure you want to delete all cart items?')">Delete All</a>
                            </button>

                            <p>Grand Total : <i class="fa-solid fa-indian-rupee-sign"></i> <span><?php echo htmlspecialchars(number_format($grand_total, 2)); ?></span></p>
                        </div>
                    </td>
                    <td class="cart-total"></td>
                </tr>
            </table>
    </main>
        
    <script src="../JS/index.js"></script> 
</body>
</html>