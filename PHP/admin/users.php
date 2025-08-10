<?php
include '../connection.php';
include 'admin_details.php'; 

$message = [];

//Delete user
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $delete_stmt = $conn->prepare("DELETE FROM `users` WHERE id = ?");
    if ($delete_stmt === false) {
        $message[] = "Error preparing delete statement: " . $conn->error;
    } else {
        $delete_stmt->bind_param("i", $delete_id);
        if ($delete_stmt->execute()) {
            $message[] = 'User deleted successfully!';
        } else {
            $message[] = 'Error deleting user: ' . $delete_stmt->error;
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
    <title>Users - MediMax.com</title>
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
           <a href="all_orders.php">Orders</a>
           <a href="users.php" class="active">Users</a>
           <a href="add_user.php">Add Users</a>
           <a href="../../index.php" target="_blank">Go To Web</a>
           <a href="?logout=true"  onclick="return confirm('Are you sure you want to log out?');">Logout
                <i class="fa-solid fa-right-from-bracket" style="color: #ffffff;"></i>
           </a>
        </nav>
    </div>

    <div class="section">

        <?php
            if (isset($message)) {
                foreach ($message as $msg) { 
                    echo '<div class="message" onclick="this.remove();">'.htmlspecialchars($msg).'</div>';
                }
            }   
        ?>

        <div class="table-container"> <table>
                <tr>
                    <th class="t-no">No</th>
                    <th>Name</th>
                    <th>Pr-Pic</th>
                    <th>Email</th>
                    <th>Role</th> <th class="t-action">Action</th>
                </tr>

                <?php
                $select_users = mysqli_query($conn, "SELECT * FROM `users`"); 
                if (!$select_users) {
                    $message[] = 'Error fetching users: ' . mysqli_error($conn);
                } else {
                    $u_no = 1;
                    while ($fetch_user = mysqli_fetch_assoc($select_users)) {
                ?>

                <tr>
                    <td><?php echo $u_no; ?></td>
                    <td><?php echo htmlspecialchars($fetch_user['name']); ?></td>
                    <td>
                        <?php if (!empty($fetch_user['image'])): ?>
                            <img src="../../Images/<?php echo htmlspecialchars($fetch_user['image']); ?>" alt="Profile" style="width: 30px; height: 30px; border-radius: 50%;">
                        <?php else: ?>
                            <span class="initials-small"><?php echo htmlspecialchars(strtoupper(substr($fetch_user['name'], 0, 1)));?></span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($fetch_user['email']); ?></td>
                    <td><?php echo htmlspecialchars($fetch_user['role']); ?></td>
                    <td>
                        <a href="edit_user.php?id=<?php echo htmlspecialchars($fetch_user['id']); ?>"><button>Edit <i class="fa-solid fa-pen-to-square"></i></button></a> <span>|</span>
                        <a href="users.php?delete=<?php echo htmlspecialchars($fetch_user['id']); ?>" 
                            onclick="return confirm('Remove user?');"><button>Delete <i class="fa-solid fa-trash"></i></button></a>
                    </td>
                </tr>
                
                <?php
                        $u_no++;
                    }
                }
                ?>
            </table>
        </div>
    </div>

    <script src="../../JS/admin.js"></script>

</body>
</html>