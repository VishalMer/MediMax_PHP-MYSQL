<?php
include '../connection.php'; 
include 'admin_details.php';
include 'validation_functions.php'; 

$message = [];
$user = [];

$userId = '';
$name = '';
$email = '';
$role = ''; 

if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    $query = "SELECT id, name, email, role FROM `users` WHERE id = ?"; 
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die("Error preparing select statement: " . $conn->error);
    }
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        $name = $user['name'];
        $email = $user['email'];
        $role = $user['role']; 
    } else {
        $message[] = "User not found!";
        header("Location: users.php");
        exit;
    }
    $stmt->close();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['id'];
    $name = trim($_POST['name']);
    $email = trim(strtolower($_POST['email']));
    $role = trim($_POST['role']); 

    $name_errors = [];
    $email_errors = [];
    $role_errors = [];
    $db_errors = [];

    // Name Validations
    if (empty($name)) {
        $name_errors[] = 'Username is required.';
    } else {
        $name_validation_result = validateUsername($name);
        if ($name_validation_result) {
            $name_errors[] = $name_validation_result;
        }
    }

    // Email Validations
    if (empty($email)) {
        $email_errors[] = 'Email is required.';
    } else {
        $email_validation_result = validateEmail($email);
        if ($email_validation_result) {
            $email_errors[] = $email_validation_result;
        }
    }

    // Role Validations
    if (empty($role)) {
        $role_errors[] = 'Role is required.';
    } else {
        $role_validation_result = validateRole($role); 
        if ($role_validation_result) {
            $role_errors[] = $role_validation_result;
        }
    }

    //Prioritize Error Message ---
    if (!empty($name_errors)) {
        $message[] = $name_errors[0]; 
    } elseif (!empty($email_errors)) {
        $message[] = $email_errors[0]; 
    } elseif (!empty($role_errors)) {
        $message[] = $role_errors[0]; 
    } else {
        $check_email_query = "SELECT id FROM `users` WHERE email = ? AND id != ?";
        $check_email_stmt = $conn->prepare($check_email_query);
        if ($check_email_stmt === false) {
            $db_errors[] = "Error preparing email check statement: " . $conn->error;
        } else {
            $check_email_stmt->bind_param("si", $email, $userId);
            $check_email_stmt->execute();
            $check_email_stmt->store_result();
            
            if ($check_email_stmt->num_rows > 0) {
                $db_errors[] = 'Another user with this email already exists!';
            }
            $check_email_stmt->close();
        }

        if (empty($db_errors)) { 
            $check_name_query = "SELECT id FROM `users` WHERE name = ? AND id != ?";
            $check_name_stmt = $conn->prepare($check_name_query);
            if ($check_name_stmt === false) {
                $db_errors[] = "Error preparing username check statement: " . $conn->error;
            } else {
                $check_name_stmt->bind_param("si", $name, $userId);
                $check_name_stmt->execute();
                $check_name_stmt->store_result();
                
                if ($check_name_stmt->num_rows > 0) {
                    $db_errors[] = 'Another user with this username already exists!';
                }
                $check_name_stmt->close();
            }
        }
        
        if (!empty($db_errors)) {
            $message[] = $db_errors[0];
        }
    }

    if (empty($message)) {
        $updateQuery = "UPDATE `users` SET name=?, email=?, role=? WHERE id=?"; 
        $updateStmt = $conn->prepare($updateQuery);
        if ($updateStmt === false) {
            $message[] = "Error preparing update statement: " . $conn->error;
        } else {
            $updateStmt->bind_param("sssi", $name, $email, $role, $userId);

            if ($updateStmt->execute()) {
                header("Location: users.php");
                exit;
            } else {
                $message[] = "Error updating user: " . $updateStmt->error;
            }
            $updateStmt->close();
        }
    }
} else {
    $message[] = "No user ID provided for editing.";
    header("Location: users.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../CSS/adminPanel.css"> <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Edit User - MediMax.com</title> </head>
<body style="background-image: url('../../Images/MediMax-BG.jpeg');">

    <div class="header">
        <a href="admin_panel.php" > <div class="user-info">
                <div class="profile-pic">
                    <?php if (!empty($image)): ?>
                        <img src="../../Images/<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($username); ?>" style="width: 50px; height: 50px; border-radius: 50%;"> <?php else: ?>
                        <span class="initials"><?php echo htmlspecialchars($firstLetter ?? '');?></span> <?php endif; ?>
                </div>
                <div class="user-name">
                    <span><?php echo htmlspecialchars($username ?? 'Guest');?></span><br> <span class="user-bio">You're <?php echo htmlspecialchars($loggedInUserRole ?? '');?>.</span> </div>
            </div>
        </a>

        <nav class="nav">
           <a href="admin_panel.php">Dashboard</a> <a href="product_list.php">Products</a> <a href="add_product.php">Add Product</a> <a href="all_orders.php">Orders</a> <a href="users.php">Users</a>
           <a href="add_user.php">Add Users</a> <a href="../../index.php" target="_blank">Go To Web</a> <a href="../../index.php?logout=<?php echo htmlspecialchars($user_id ?? ''); ?>" onclick="return confirm('Are you sure you want to logout ??');">LogOut <i class="fa-solid fa-right-from-bracket" style="color: #ffffff;"></i></a>
        </nav>
    </div>

    <div class="section">
        <?php
            
            if (!empty($message)) {
                echo '<div class="message msg" onclick="this.remove();">' . htmlspecialchars($message[0]) . '</div>';
            }   
        ?>

        <div class="container edit-product" style="backdrop-filter: blur(10px);">
            <h2>Edit User Details</h2>
            <form action="edit_user.php" method="POST"> <input type="hidden" name="id" value="<?php echo htmlspecialchars($userId); ?>"> <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($name); ?>"> </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>"> </div>
                <div class="form-group">
                    <label for="role">Role:</label> <input type="text" name="role" id="role" value="<?php echo htmlspecialchars($role); ?>" placeholder="customer (default)" list="roles"> <datalist id="roles">
                        <option value="admin">
                        <option value="customer">
                    </datalist>
                </div>
                <button type="submit">Update User</button>
            </form>
            <a href="users.php"><button>Back to User List</button></a>
        </div>
    </div>
</body>
</html>