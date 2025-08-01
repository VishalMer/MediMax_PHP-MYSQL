<?php

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); 

include 'connection.php'; 
include 'user_session.php'; 

if ($user_id === null) {
    $_SESSION['message'][] = 'Please login to view your wishlist.';
    header('Location: login_form.php'); 
    exit(); 
}

if (isset($_GET['remove'])) {
    $remove_id = mysqli_real_escape_string($conn, $_GET['remove']);
    mysqli_query($conn, "DELETE FROM `wishlist` WHERE id = '$remove_id' AND user_id = '$user_id'") or die('Query failed: ' . mysqli_error($conn));
    $_SESSION['message'][] = 'Wishlist item deleted!'; 

    header('Location: wishlist.php');
    exit();
}

// Delete all items from wishlist
elseif (isset($_GET['delete_all'])) {
    mysqli_query($conn, "DELETE FROM `wishlist` WHERE user_id = '$user_id'") or die('Query failed: ' . mysqli_error($conn));
    $_SESSION['message'][] = 'All items deleted from wishlist!'; 
    header('Location: wishlist.php');
    exit();
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <title>Your Wishlist - MediMax.com</title>
    <link rel="stylesheet" href="../CSS/style.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>

    <div class="bcimage">
        <img src="../Images/MediMax-BG.jpeg" alt="MediMax Background Image"> 
    </div>

    <header class="header">
        <a href="../index.php" class="logo"> <img src="../Images/MediMax_Logo.png" alt="MediMax"> </a>

        <div class="search-bar">
            <form method="post" action="wishlist.php"> 
                <input type="search" name="search_input" placeholder="Search MediMax.com" id="search-input">
                <button type="submit" name="search" class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
        </div>
        
        <nav class="nav">
            <a href="../index.php">Home</a> 
            <a href="Products.php">Products</a>
            <a href="Orders.php">Orders</a>
            <a href="AboutUs.php">About Us</a>
            <a href="Contact.php">Contact</a>
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

    <?php
    
    if (!empty($_SESSION['message'])) { 
        foreach ($_SESSION['message'] as $msg) {
            echo '<div class="cart-msg message" onclick="this.remove();">' . htmlspecialchars($msg) . '</div>';
        }
        unset($_SESSION['message']);
    }
    ?>

    <main class="cart">
        <table class="cart-conatiner wishlist">
            <tr class="cart-header">
                <th class="cart-title" colspan="4">Your Wishlist </th>
            </tr>
            <tr>
                <td class="cart-content">
                    <div class="cart-boxes">

                    <?php
                        $wishlist_query_db = mysqli_query($conn, "SELECT * FROM `wishlist` WHERE user_id = '$user_id'") or die("query failed: " . mysqli_error($conn));
                        if (mysqli_num_rows($wishlist_query_db) > 0) {
                            while ($fetch_wishlist = mysqli_fetch_assoc($wishlist_query_db)) {
                        ?>
                        <div class="cart-box">
                            <img src="../Images/<?php echo htmlspecialchars($fetch_wishlist['image']); ?>" alt="<?php echo htmlspecialchars($fetch_wishlist['name']); ?>"> 
                            <div class="product-detail">
                                <h2><?php echo htmlspecialchars($fetch_wishlist['name']); ?></h2><br>
                                <pre>Price : <span><i class="fa-solid fa-indian-rupee-sign"></i><?php echo htmlspecialchars($fetch_wishlist['price']); ?></span>
                                </pre><br>

                                <div class="cart-quantity">
                                    <a href="Wishlist.php?remove=<?php echo htmlspecialchars($fetch_wishlist['id']); ?>"
                                    class="remove-item" onclick="return confirm('Remove item from wishlist?');">
                                    <i class="fa-regular fa-trash-can" style="color: #094e7e;"></i> Remove</a>
                                </div>
                            </div>
                        </div>

                        <?php
                                }
                            } else {
                        ?>
                            <div class="empty-cart">
                                <p> Your wishlist is empty </p>
                                <a href='Products.php'><img src="../Images/Empty Cart.GIF" alt="Empty Cart"></a><br> 
                                <button><a href='Products.php'>Add Products Now</a></button>
                            </div>
                        <?php
                            }
                        ?>
                    </div>
                </td>
            </tr>
        
            <tr class = "cart-bottom">
                <td class="cart-options">
                    <div class="cart-footer">
                        <button><a href="Products.php">Wishlist More Products...</a></button>
                        <?php
                            $current_wishlist_count = mysqli_query($conn, "SELECT COUNT(*) FROM `wishlist` WHERE user_id = '$user_id'") or die("query failed: " . mysqli_error($conn));
                            $row_count = mysqli_fetch_row($current_wishlist_count);
                            $is_wishlist_empty = ($row_count[0] == 0);
                        ?>
                        <button class="<?php echo $is_wishlist_empty ? 'disabled' : ''; ?>">
                            <a href="Wishlist.php?delete_all" onclick="return confirm('Are you sure you want to delete all wishlist items?')">Delete All</a>
                        </button>
                    </div>
                </td>
                <td class="cart-total"></td>
            </tr>
        </table>
    </main>
        
    <script src="../JS/index.js"></script> 
</body>
</html>