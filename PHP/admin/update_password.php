<?php

include '../connection.php';
include 'admin_details.php'; 
include 'validation_functions.php';

$message = []; 

// Initialize variables to retain form values
$current_password = '';
$new_password = '';
$confirm_password = '';

// Handle password update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // 1. Current password check 
    if (empty($current_password)) {
        $message[] = 'Please enter your current password.';
    } elseif (!isset($fetch_user['password']) || md5($current_password) !== $fetch_user['password']) {
        $message[] = 'Incorrect current password.';
    } 
    // 2. New password validation
    elseif (empty($new_password)) {
        $message[] = 'Please enter a new password.';
    } elseif ($validation_error = validatePassword($new_password)) { 
        $message[] = $validation_error;
    } 
    // 3. Confirm password check
    elseif (empty($confirm_password)) {
        $message[] = 'Please enter your new password again to confirm.';
    }
    // 4. Password match check
    elseif ($new_password !== $confirm_password) {
        $message[] = 'New password and confirm password do not match.';
    }
    
    
    if (empty($message)) { //means no errors

        $hashed_new_password = md5($new_password);
        
        $updateQuery = "UPDATE `users` SET password=? WHERE id=?";
        $updateStmt = $conn->prepare($updateQuery);

        if ($updateStmt === false) {
            $message[] = "Error preparing update statement: " . $conn->error;
        } else {
            $updateStmt->bind_param("si", $hashed_new_password, $user_id);

            if ($updateStmt->execute()) {
                $_SESSION['message'][] = 'Password updated successfully!';
                
                if ($loggedInUserRole === 'admin' || $loggedInUserRole === 'owner') {
                    header("Location: admin_panel.php");
                } else {
                    header("Location: ../../index.php");
                }
                exit;
            } else {
                $message[] = "Error updating password: " . $updateStmt->error;
            }
            $updateStmt->close();
        }
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
    <title>Change Password - MediMax.com</title>
</head>
<body>

<?php
if ($loggedInUserRole === 'admin' || $loggedInUserRole === 'owner') {

?>
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
        <a href="update_password.php" class="active">Update Password</a>
        <a href="product_list.php">Products</a>
        <a href="add_product.php">Add Product</a>
        <a href="all_orders.php">Orders</a>
        <a href="users.php">Users</a>
        <a href="add_user.php">Add Users</a>
        <a href="../../index.php" target="_blank">Go To Web</a>
        <a href="?logout=true"  onclick="return confirm('Are you sure you want to log out?');">Logout
            <i class="fa-solid fa-right-from-bracket" style="color: #ffffff;"></i>
        </a>
    </nav>
</div>
<?php
} 
?>

<div class="section">
    <?php
    
    if (!empty($message)) {
        if ($loggedInUserRole === 'admin' || $loggedInUserRole === 'owner') {
        echo '<div class="message pass-msg1" onclick="this.remove();">' . htmlspecialchars($message[0]) . '</div>';
        } else {
        echo '<div class="message pass-msg2" onclick="this.remove();">' . htmlspecialchars($message[0]) . '</div>';
        }
    } 
    ?>
    
    <div class="container update-pass">
        <h2>Change Password</h2>
        <form action="update_password.php" method="POST">
            <div class="main-ctr">
                <div class="ctr">
                    <div class="form-group">
                        <label for="current_password">Current Password:</label>
                        <input type="password" name="current_password" id="current_password" value="<?php echo htmlspecialchars($current_password); ?>">
                    </div>
                    <div class="form-group">
                        <label for="new_password">New Password:</label>
                        <input type="password" name="new_password" id="new_password" value="<?php echo htmlspecialchars($new_password); ?>">
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password:</label>
                        <input type="password" name="confirm_password" id="confirm_password" value="<?php echo htmlspecialchars($confirm_password); ?>">
                    </div>
                </div>
            </div>

            <div class="buttons">
                
                <button type="submit">Update Password</button>

                <?php
                if ($loggedInUserRole === 'admin' || $loggedInUserRole === 'owner') {
                    echo '<a href="admin_panel.php"><button type="button">Back to Home</button></a>';
                } else {
                    echo '<a href="../../index.php"><button type="button">Back to Home</button></a>';
                }
                ?>

            </div>
        </form>
    </div>
</div>
</body>
</html>