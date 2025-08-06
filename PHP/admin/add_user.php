<?php
include '../connection.php';
include 'admin_details.php';
include 'validation_functions.php';

$message = [];

$name = '';
$email = '';
$new_user_role = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get new user details
    $name = trim($_POST['name']); 
    $email = trim(strtolower($_POST['email']));
    $new_user_role = trim($_POST['role']); 
    $password = $_POST['password']; 

    if (empty($new_user_role)) {
        $new_user_role = 'customer'; 
    }

    $name_errors = [];
    $email_errors = [];
    $password_errors = [];
    $db_errors = [];

    // Name Validation
    if (empty($name)) {
        $name_errors[] = 'Username is required.';
    } else {
        $name_validation_result = validateUsername($name);
        if ($name_validation_result) {
            $name_errors[] = $name_validation_result;
        }
    }

    // Email Validation
    if (empty($email)) {
        $email_errors[] = 'Email is required.';
    } else {
        $email_validation_result = validateEmail($email);
        if ($email_validation_result) {
            $email_errors[] = $email_validation_result;
        }
    }

    // Password Validation
    if (empty($password)) {
        $password_errors[] = 'Password is required.';
    } else {
        $password_validation_result = validatePassword($password);
        if ($password_validation_result) {
            $password_errors[] = $password_validation_result;
        }
    }

    //priotize errors 
    if (!empty($name_errors)) {
        $message[] = $name_errors[0]; 
    } elseif (!empty($email_errors)) {
        $message[] = $email_errors[0]; 
    } elseif (!empty($password_errors)) {
        $message[] = $password_errors[0];
    } else {
        
        // Check if email already exists
        $check_email_query = "SELECT id FROM `users` WHERE email = ?";
        $check_email_stmt = $conn->prepare($check_email_query);
        if ($check_email_stmt === false) {
            $db_errors[] = "Error preparing email check statement: " . $conn->error;
        } else {
            $check_email_stmt->bind_param("s", $email);
            $check_email_stmt->execute();
            $check_email_stmt->store_result();
            
            if ($check_email_stmt->num_rows > 0) {
                $db_errors[] = 'User with this email already exists!';
            }
            $check_email_stmt->close();
        }

        // Check if username already exists
        if (empty($db_errors)) { 
            $check_name_query = "SELECT id FROM `users` WHERE name = ?";
            $check_name_stmt = $conn->prepare($check_name_query);
            if ($check_name_stmt === false) {
                $db_errors[] = "Error preparing username check statement: " . $conn->error;
            } else {
                $check_name_stmt->bind_param("s", $name);
                $check_name_stmt->execute();
                $check_name_stmt->store_result();
                
                if ($check_name_stmt->num_rows > 0) {
                    $db_errors[] = 'User with this username already exists!';
                }
                $check_name_stmt->close();
            }
        }
        
        if (!empty($db_errors)) {
            $message[] = $db_errors[0];
        }
    }

    
    if (empty($message)) {
        
        $insertQuery = "INSERT INTO `users` (name, email, role, password) VALUES (?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertQuery);
        
        if ($insertStmt === false) {
            $message[] = "Error preparing insert statement: " . $conn->error; 
        } else {
            $hashedPassword = md5($password); 
            
            $insertStmt->bind_param("ssss", $name, $email, $new_user_role, $hashedPassword);

            if ($insertStmt->execute()) {
                header("Location: users.php"); 
                exit; 
            } else {
                $message[] = "Error adding user: " . $insertStmt->error;
            }
            $insertStmt->close();
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
    <title>Add New User - MediMax.com</title>
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
           <a href="users.php">Users</a>
           <a href="add_user.php" class="active">Add Users</a>
           <a href="../../index.php" target="_blank">Go To Web</a>
           <a href="?logout=true"  onclick="return confirm('Are you sure you want to log out?');">Logout
                <i class="fa-solid fa-right-from-bracket" style="color: #ffffff;"></i>
           </a>
        </nav>
    </div>

    <div class="section">

<?php
    // Display only the first error message
    if (!empty($message)) {
        echo '<div class="message msg" onclick="this.remove();">' . htmlspecialchars($message[0]) . '</div>';
    }   
?>

        <div class="container" style="backdrop-filter: blur(10px); margin-top: -8px;">
            <h2>Add New User</h2>
            <form action="add_user.php" method="POST">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($name); ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="text" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>">
                </div>
                <div class="form-group">
                    <label for="role">Role:</label>
                    <input type="text" name="role" id="role" value="<?php echo htmlspecialchars($new_user_role); ?>" placeholder="customer (default)" list="roles">
                    <datalist id="roles">
                        <option value="customer">
                        <option value="admin">
                        <option value="owner">
                    </datalist>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password">
                </div>
                <button type="submit">Add User</button>
            </form>
            <a href="users.php"><button>Back to User List</button></a>
        </div>
    </div>

<script src="../../JS/admin.js"></script>

</body>
</html>