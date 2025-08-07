<?php
include '../connection.php';
include 'admin_details.php'; 

$message = [];

// Delete an order
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];

    $delete_stmt = $conn->prepare("DELETE FROM `orders` WHERE id = ?");
    if ($delete_stmt === false) {
        $message[] = "Error preparing delete statement: " . $conn->error;
    } else {
        $delete_stmt->bind_param("i", $delete_id); 
        if ($delete_stmt->execute()) {
            $message[] = 'Order deleted successfully!';
        } else {
            $message[] = 'Error deleting order: ' . $delete_stmt->error;
        }
        $delete_stmt->close();
    }
}

// Mark delivery as done
if (isset($_GET['deliver'])) {
    $deliver_id = $_GET['deliver'];
    
    $update_deliver_stmt = $conn->prepare("UPDATE `orders` SET delevery = 'Delivered' WHERE id = ?");
    if ($update_deliver_stmt === false) {
        $message[] = "Error preparing delivery update statement: " . $conn->error;
    } else {
        $update_deliver_stmt->bind_param("i", $deliver_id);
        if ($update_deliver_stmt->execute()) {
            $message[] = 'Delivery marked as delivered!';
        } else {
            $message[] = 'Error marking delivery as delivered: ' . $update_deliver_stmt->error;
        }
        $update_deliver_stmt->close();
    }
}

// Mark payment as done
if (isset($_GET['payment'])) {
    $payment_id = $_GET['payment'];
    
    $update_payment_stmt = $conn->prepare("UPDATE `orders` SET payment = 'Completed' WHERE id = ?");
    if ($update_payment_stmt === false) {
        $message[] = "Error preparing payment update statement: " . $conn->error;
    } else {
        $update_payment_stmt->bind_param("i", $payment_id);
        if ($update_payment_stmt->execute()) {
            $message[] = 'Payment marked as completed!';
        } else {
            $message[] = 'Error marking payment as completed: ' . $update_payment_stmt->error;
        }
        $update_payment_stmt->close();
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
    <title>Admin Orders - MediMax.com</title>
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
           <a href="update_profile.php">Update Profile</a>
           <a href="update_password.php">Update Password</a>
           <a href="product_list.php">Products</a>
           <a href="add_product.php">Add Product</a>
           <a href="all_orders.php" class="active">Orders</a>
           <a href="users.php">Users</a>
           <a href="add_user.php">Add Users</a>
           <a href="../../index.php" target="_blank">Go To Web</a>
           <a href="?logout=true"  onclick="return confirm('Are you sure you want to log out?');">Logout
                <i class="fa-solid fa-right-from-bracket" style="color: #ffffff;"></i>
           </a>
        </nav>
    </div>

    <div class="section">

        <?php
            if(isset($message)){
                foreach($message as $msg){
                echo '<div class="message" onclick="this.remove();">'.htmlspecialchars($msg).'</div>';
                }
            }   
        ?>

        <div class="table-container"> <table>
                <tr>
                    <th class="t-no">Order ID</th>
                    <th>User ID</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Payment</th>
                    <th>Delivery</th>
                    <th class="t-action">Action</th>
                </tr>

                <?php
                // Fetch orders from the orders table using prepared statement for consistency
                $select_orders_stmt = $conn->prepare("SELECT id, user_id, name, quantity, payment, delevery FROM `orders`");
                
                if ($select_orders_stmt === false) {
                    $message[] = "Error preparing order fetch statement: " . $conn->error;
                } else {
                    $select_orders_stmt->execute();
                    $result_orders = $select_orders_stmt->get_result();

                    if ($result_orders->num_rows > 0) {
                        while ($fetch_order = $result_orders->fetch_assoc()) {
                ?>

                <tr>
                    <td><?php echo htmlspecialchars($fetch_order['id']); ?></td>
                    <td><?php echo htmlspecialchars($fetch_order['user_id']); ?></td>
                    <td><?php echo htmlspecialchars($fetch_order['name']); ?></td>
                    <td><?php echo htmlspecialchars($fetch_order['quantity']); ?></td>
                    <td>
                        <?php echo htmlspecialchars($fetch_order['payment']); ?>
                        <?php if ($fetch_order['payment'] !== 'Completed'): ?> <span>|</span>
                        <a href="all_orders.php?payment=<?php echo htmlspecialchars($fetch_order['id']); ?>" 
                            onclick="return confirm('Mark this payment as completed?');"><button>Done <i class="fa-regular fa-circle-check"></i></button></a>
                        <?php endif; ?>
                    </td>
                    <td>
                         <?php echo htmlspecialchars($fetch_order['delevery']); ?>
                        <?php if ($fetch_order['delevery'] !== 'Delivered'): ?> <span>|</span>
                        <a href="all_orders.php?deliver=<?php echo htmlspecialchars($fetch_order['id']); ?>" 
                            onclick="return confirm('Mark this delivery as delivered?');"><button>Done <i class="fa-regular fa-circle-check"></i></button></a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="all_orders.php?delete=<?php echo htmlspecialchars($fetch_order['id']); ?>" 
                            onclick="return confirm('Remove this order?');"><button>Remove <i class="fa-solid fa-trash"></i></button></a>
                    </td>
                </tr>
                
                <?php
                        }
                    } else {
                        echo '<tr><td colspan="7">No orders found.</td></tr>';
                    }
                    $select_orders_stmt->close();
                }
                ?>
            </table>
        </div>
    </div>

    <script src="../../JS/admin.js"></script>

</body>
</html>