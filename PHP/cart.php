<?php

include 'connection.php';
include 'user_details.php';

// Update cart information
if (isset($_POST['update_cart'])) {
    $update_quantity = $_POST['cart_quantity'];
    $update_id = $_POST['cart_id'];
    mysqli_query($conn, "UPDATE `cart` SET quantity = '$update_quantity' WHERE id = '$update_id'") or die('Query failed');
    $message[] = 'Cart quantity updated!';
}

// Delete item from cart
elseif (isset($_GET['remove'])) {
    $remove_id = $_GET['remove'];
    mysqli_query($conn, "DELETE FROM `cart` WHERE id = '$remove_id'") or die('Query failed');
    $message[] = 'Cart item deleted!';
}

// Delete all items from cart
elseif (isset($_GET['delete_all'])) {
    mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('Query failed');
    $message[] = 'All items deleted from cart!';
}

// Buy All Now - New Functionality
if (isset($_POST['buy_all'])) {
    // Fetch all products from the cart
    $cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('Query failed');
    
    if (mysqli_num_rows($cart_query) > 0) {
        // Loop through each product in the cart
        while ($fetch_cart = mysqli_fetch_assoc($cart_query)) {
            $name = $fetch_cart['name'];
            $price = $fetch_cart['price'];
            $image = $fetch_cart['image'];
            $quantity = $fetch_cart['quantity'];
            
            // Insert into orders table
            $insert_order = mysqli_query($conn, "INSERT INTO `orders` (user_id, name, price, image, quantity) VALUES ('$user_id', '$name', '$price', '$image', '$quantity')") or die('Insert failed');
        }

        // Clear the cart
        mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('Cart clear failed');
        $message[] = 'All products purchased successfully!';

    } else {
        $message[] = 'Your cart is empty!';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cart - MediMax.com</title>
    <link rel="stylesheet" href="../CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    
    <div class="bcimage">
        <img src="../Images/MediMax-BG.jpeg" alt="MediMax Background Image" >
    </div>
    <header class="header">
        <a href="Index.php" class="logo">
            <img src="../Images/MediMax_Logo.png" alt="MediMax">
        </a>

        <div class="search-bar ">
            <form method="post" action="">
                <input type="search" placeholder="Search MediMax.com" id="search-input">
                <button type="submit" class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
        </div>
        
        <nav class="nav">
           <a href="index.php">Home</a>
           <a href="products.php">Products</a>
           <a href="orders.php">Orders</a>
           <a href="aboutus.php">About Us</a>
           <a href="contact.php">Contact</a>
        </nav>

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
            <a href="index.php?logout=<?php echo $user_id; ?>" 
            onclick="return confirm('Are you sure you want to log out ??');">Log Out <i class="fa-solid fa-right-from-bracket" style="color: #ffffff;"></i></a>
        </button>
    </div>

    <main class="cart">

    <!-- Cart update quantity message -->
<?php
if(isset($message)){
    foreach($message as $message){
        echo '<div class="cart-msg message" onclick="this.remove();">'.$message.'</div>';
    }
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
                            $cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die("query failed");
                            if (mysqli_num_rows($cart_query) > 0) {
                                while ($fetch_cart = mysqli_fetch_assoc($cart_query)) {
                                    $sub_total = $fetch_cart['price'] * $fetch_cart['quantity'];
                                    $grand_total += $sub_total;
                        ?>
                        <div class="cart-box">
                            <img src="../Images/<?php echo $fetch_cart['image'] ?>">
                            <div class="product-detail">
                                <h2><?php echo $fetch_cart['name'] ?></h2><br>
                                <pre>Price :          Sub Total : <br><span><i class="fa-solid fa-indian-rupee-sign"></i><?php echo $fetch_cart['price'] ?>             <i class="fa-solid fa-indian-rupee-sign"></i><?php echo $sub_total;?></span></pre><br>

                                <p>Quantity :</p>
                                <div class="cart-quantity">
                                    <form method="post" action="">
                                        <input type="hidden" name="cart_id" value="<?php echo $fetch_cart['id'] ?>">
                                        <input type="number" min="1" max="99" name="cart_quantity" value="<?php echo $fetch_cart['quantity'] ?>" id="quantity">
                                        <input type="submit" name="update_cart" value="Update" class="updatebtn">
                                    </form>
                                    <a href="Cart.php?remove=<?php echo $fetch_cart['id']; ?>"
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
                                <a href='Products.php'><img src="./Images/Empty Cart.GIF" alt="Empty Cart"></a><br>
                                <button><a href='Products.php'>Continue Shopping</a></button>
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
                        <button><a href="Products.php">Continue Shopping...</a></button>
                        <form method="post" action="">
                            <button type="submit" name="buy_all" class="<?php echo ($grand_total > 0) ? '' : 'disabled'; ?>">Buy All Now</button>
                        </form>
                        <button class="<?php echo ($grand_total > 0) ? '' : 'disabled'; ?>"><a href="Cart.php?delete_all" onclick="return confirm('Are you sure you want to delete all cart items?')">Delete All</a></button>

                        <p>Grand Total : <i class="fa-solid fa-indian-rupee-sign"></i> <span><?php echo $grand_total; ?></span></p>
                    </div>
                </td>
                <td class="cart-total"></td>
            </tr>
        </table>
    </main>
        
    <script src="index.js"></script>

</body>
</html>