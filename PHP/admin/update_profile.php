<?php
include '../connection.php'; 
include 'admin_details.php';
include 'validation_functions.php';

$message = []; 

$name = isset($fetch_user['name']) ? $fetch_user['name'] : ''; 
$email = isset($fetch_user['email']) ? $fetch_user['email'] : ''; 
$current_image_name = isset($fetch_user['image']) ? $fetch_user['image'] : ''; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submitted_name = trim($_POST['name']);
    $submitted_email = trim(strtolower($_POST['email']));
    $submitted_password = $_POST['password']; // This is the password entered by the user for verification
    
    // Retain submitted values for display if validation fails
    $name = $submitted_name;
    $email = $submitted_email;

    // Name Validations
    if (empty($submitted_name)) {
        $message[] = 'Username is required.';
    } else {
        $name_validation_result = validateUsername($submitted_name);
        if ($name_validation_result) {
            $message[] = $name_validation_result;
        }
    }

    // Email Validations
    if (empty($submitted_email)) {
        $message[] = 'Email is required.';
    } else {
        $email_validation_result = validateEmail($submitted_email);
        if ($email_validation_result) {
            $message[] = $email_validation_result;
        }
    }

    // Password Validation (for the entered current password)
    if (empty($submitted_password)) {
        $message[] = 'Current password is required to update profile.';
    } else {
        
        if (!isset($fetch_user['password']) || md5($submitted_password) !== $fetch_user['password']) {
            $message[] = 'Incorrect current password.';
        }
    }

    $new_image_uploaded = false;
    $image_name_for_db = $current_image_name; 
    $old_image_to_delete = ''; 

    if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) {
        $new_image_uploaded = true;
        $image_name_for_db = $_FILES['image']['name']; 
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_size = $_FILES['image']['size'];
        $image_error = $_FILES['image']['error'];

        $target_dir = "../../Images/";
        $target_file = $target_dir . basename($image_name_for_db);
        $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Image validation logic
        if ($image_error !== UPLOAD_ERR_OK) {
            $image_errors = [
                UPLOAD_ERR_INI_SIZE => 'Image file is too large (exceeds server limit).',
                UPLOAD_ERR_FORM_SIZE => 'Image file is too large (exceeds server limit).',
                UPLOAD_ERR_PARTIAL => 'Image file was only partially uploaded.',
                UPLOAD_ERR_NO_FILE => 'No image file was uploaded. Please select an image.',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder for image uploads.',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write image file to disk.',
                UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the image upload.'
            ];
            $message[] = $image_errors[$image_error] ?? 'Unknown error uploading image.';
        } elseif ($image_size > 5000000) { 
            $message[] = 'Image file is too large. Max 5MB.';
        } elseif (!in_array($image_file_type, ['jpg', 'jpeg', 'png', 'gif'])) {
            $message[] = 'Only JPG, JPEG, PNG, & GIF files are allowed for images.';
        }
        
        // If no image errors, prepare to delete old image
        if (empty($message) && !empty($current_image_name)) {
            $old_image_to_delete = $current_image_name; 
        }
    }

    if (empty($message)) {
        // Check if email already exists for ANOTHER user
        $check_email_query = "SELECT id FROM `users` WHERE email = ? AND id != ?";
        $check_email_stmt = $conn->prepare($check_email_query);
        if ($check_email_stmt === false) {
            $message[] = "Error preparing email check statement: " . $conn->error;
        } else {
            $check_email_stmt->bind_param("si", $submitted_email, $user_id);
            $check_email_stmt->execute();
            $check_email_stmt->store_result();
            if ($check_email_stmt->num_rows > 0) {
                $message[] = 'Another user with this email already exists!';
            }
            $check_email_stmt->close();
        }

        // Check if username (name field) already exists for ANOTHER user
        if (empty($message)) {
            $check_name_query = "SELECT id FROM `users` WHERE name = ? AND id != ?";
            $check_name_stmt = $conn->prepare($check_name_query);
            if ($check_name_stmt === false) {
                $message[] = "Error preparing username check statement: " . $conn->error;
            } else {
                $check_name_stmt->bind_param("si", $submitted_name, $user_id);
                $check_name_stmt->execute();
                $check_name_stmt->store_result();
                if ($check_name_stmt->num_rows > 0) {
                    $message[] = 'Another user with this username already exists!';
                }
                $check_name_stmt->close();
            }
        }
    }
    
    if (empty($message)) {
        $updateQuery = "UPDATE `users` SET name=?, email=?";
        $bind_types = "ss";
        $bind_params = [&$submitted_name, &$submitted_email];

        if ($new_image_uploaded) {
            $updateQuery .= ", image=?";
            $bind_types .= "s";
            $bind_params[] = &$image_name_for_db;
        }

        $updateQuery .= " WHERE id=?";
        $bind_types .= "i";
        $bind_params[] = &$user_id;

        $updateStmt = $conn->prepare($updateQuery);
        if ($updateStmt === false) {
            $message[] = "Error preparing update statement: " . $conn->error;
        } else {
            call_user_func_array([$updateStmt, 'bind_param'], array_merge([$bind_types], $bind_params));

            if ($updateStmt->execute()) {
                if ($new_image_uploaded) {
                    if (move_uploaded_file($image_tmp_name, $target_file)) {
                        if (!empty($old_image_to_delete) && $old_image_to_delete !== $image_name_for_db && file_exists($target_dir . $old_image_to_delete)) {
                            unlink($target_dir . $old_image_to_delete);
                        }
                    } else {
                        $message[] = "Error moving uploaded image. Check directory permissions.";
                    }
                }
                
                $_SESSION['message'][] = 'Profile updated successfully!';
                
                if ($loggedInUserRole === 'admin' || $loggedInUserRole === 'owner') {
                    header("Location: admin_panel.php");
                } else {
                    header("Location: ../../index.php");
                }
                exit;
            } else {
                $message[] = "Error updating profile: " . $updateStmt->error;
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
    <title>Edit Profile - MediMax.com</title>
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
        <a href="update_profile.php" class="active">Update Profile</a>
        <a href="update_password.php">Update Password</a>
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
        foreach ($message as $msg) {
            if ($loggedInUserRole === 'admin' || $loggedInUserRole === 'owner') {
            echo '<div class="message pr-msg1" onclick="this.remove();">' . htmlspecialchars($msg) . '</div>';
            } else {
            echo '<div class="message pr-msg2" onclick="this.remove();">' . htmlspecialchars($msg) . '</div>';
            }
        }
    } 
    ?>
    
    <div class="container Update-pr">
        <h2>Edit Profile</h2>
        <form action="update_profile.php" method="POST" enctype="multipart/form-data">
            <div class="main-ctr">
                <div class="ctr">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($name); ?>">
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>">
                    </div>
                    <div class="form-group">
                        <label for="password">Current Password:</label>
                        <input type="password" name="password" id="password">
                    </div>
                </div>

                <div class="ctr">
                    <div class="form-group">
                        <label for="image">Change Image:</label>
                        <input type="file" name="image" id="image" accept="image/*">
                        <br>
                        <?php if (!empty($current_image_name)): ?>
                            <div class="current-img">
                                <label>Current Image:</label><br>
                                <img src="../../Images/<?php echo htmlspecialchars($current_image_name); ?>" alt="Current Profile Image" style="max-width: 150px; max-height: 150px;">
                            </div>
                        <?php else: ?>
                            <p><br><br><br>No image available.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="buttons">
                <button type="submit">Update Profile</button>
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