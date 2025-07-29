<?php

include '../connection.php';
include 'admin_details.php'; // This file now sets $loggedInUserRole, $username, $firstLetter, $image, $user_id

// Initialize $message array for notifications
$message = [];

// Delete item from products
if(isset($_GET['delete'])){
    $delete_id = $_GET['delete'];
    
    // Using prepared statement for delete for security
    $delete_stmt = $conn->prepare("DELETE FROM `products` WHERE id = ?");
    if ($delete_stmt === false) {
        $message[] = "Error preparing delete statement: " . $conn->error;
    } else {
        $delete_stmt->bind_param("i", $delete_id); // 'i' indicates integer type for $delete_id
        if ($delete_stmt->execute()) {
            $message[] = 'Product deleted successfully!';
        } else {
            $message[] = 'Error deleting product: ' . $delete_stmt->error;
        }
        $delete_stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../CSS/adminPanel.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Products List - MediMax.com</title>
</head>
<body style="background-image: url('../../Images/MediMax-BG.jpeg');">

    <div class="header">
        <a href="admin_panel.php" >
            <div class="user-info">
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
           <a href="admin_panel.php">Dashboard</a>
           <a href="product_list.php" class="active">Products</a> <a href="add_product.php">Add Product</a> <a href="all_orders.php">Orders</a> <a href="users.php">Users</a>
           <a href="add_user.php">Add Users</a> <a href="../../index.php" target="_blank">Go To Web</a>
           <a href="../../index.php?logout=<?php echo htmlspecialchars($user_id ?? ''); ?>" 
           onclick="return confirm('Are you sure you want to logout ??');">LogOut <i class="fa-solid fa-right-from-bracket" style="color: #ffffff;"></i></a>
        </nav>
    </div>

    <div class="section">

        <?php
            if(isset($message)){
                foreach($message as $msg){ // Changed loop variable to $msg to avoid conflict
                echo '<div class="message" onclick="this.remove();">'.htmlspecialchars($msg).'</div>';
                }
            }   
        ?>

        <div class="table-container"> <table>
                <tr>
                    <th class="t-no">No</th>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Image</th>
                    <th class="t-action">Action</th>
                </tr>

                <?php
                // Fetch products from the products table using prepared statement for consistency
                $select_product_stmt = $conn->prepare("SELECT id, name, price, image FROM `products`");
                
                if ($select_product_stmt === false) {
                    $message[] = "Error preparing product fetch statement: " . $conn->error;
                } else {
                    $select_product_stmt->execute();
                    $result_products = $select_product_stmt->get_result();

                    if ($result_products->num_rows > 0) {
                        $p_no = 1;
                        while ($fetch_product = $result_products->fetch_assoc()) {
                ?>

                <tr>
                    <td><?php echo $p_no; ?></td>
                    <td><?php echo htmlspecialchars($fetch_product['name']); ?></td>
                    <td><?php echo htmlspecialchars($fetch_product['price']); ?></td>
                    <td><img src="../../Images/<?php echo htmlspecialchars($fetch_product['image']); ?>" alt="<?php echo htmlspecialchars($fetch_product['name']); ?>" style="width: 80px; height: 80px; object-fit: cover;"></td>
                    <td>
                        <a href="edit_product.php?id=<?php echo htmlspecialchars($fetch_product['id']); ?>"><button>Edit <i class="fa-solid fa-pen-to-square"></i></button></a> <span>|</span> 
                        <a href="product_list.php?delete=<?php echo htmlspecialchars($fetch_product['id']); ?>" onclick="return confirm('Remove item from products?');"><button>Delete <i class="fa-solid fa-trash"></i></button></a>
                    </td>
                </tr>
                
                <?php
                            $p_no++;
                        }
                    } else {
                        echo '<tr><td colspan="5">No products found.</td></tr>';
                    }
                    $select_product_stmt->close();
                }
                ?>
            </table>
        </div>
    </div>

    <script src="../../JS/admin.js"></script> </body>
</html>