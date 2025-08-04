<?php
include '../connection.php';
include 'admin_details.php'; 

$message = [];

// Initialize variables to retain form values
$name = '';
$price = ''; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']); 
    $price = $_POST['price'];    
    
    $image_name = $_FILES['image']['name'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_size = $_FILES['image']['size'];
    $image_error = $_FILES['image']['error'];

    $target_dir = "../../Images/";
    $target_file = $target_dir . basename($image_name);
    $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));


    if (empty($name)) {
        $message[] = 'Product name cannot be empty.';
    } elseif (!is_numeric($price) || $price < 0) {
        $message[] = 'Price must be a positive number.';
    } else {
        // Advanced image validation
        if ($image_error !== UPLOAD_ERR_OK) {
            // Specific error messages for common upload issues
            switch ($image_error) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $message[] = 'Image file is too large (exceeds server limit).';
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $message[] = 'Image file was only partially uploaded.';
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $message[] = 'No image file was uploaded. Please select an image.'; 
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $message[] = 'Missing a temporary folder for image uploads.';
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $message[] = 'Failed to write image file to disk.';
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $message[] = 'A PHP extension stopped the image upload.';
                    break;
                default:
                    $message[] = 'Unknown error uploading image: ' . $image_error; 
            }
        } elseif ($image_size > 5000000) { 
            $message[] = 'Image file is too large. Max 5MB.';
        } elseif (!in_array($image_file_type, ['jpg', 'jpeg', 'png', 'gif'])) {
            $message[] = 'Only JPG, JPEG, PNG, & GIF files are allowed for images.';
        } else {
            // Check if product name already exists
            $check_name_query = "SELECT id FROM `products` WHERE name = ?";
            $check_name_stmt = $conn->prepare($check_name_query);
            if ($check_name_stmt === false) {
                $message[] = "Error preparing name check statement: " . $conn->error;
            } else {
                $check_name_stmt->bind_param("s", $name);
                $check_name_stmt->execute();
                $check_name_stmt->store_result();

                if ($check_name_stmt->num_rows > 0) {
                    $message[] = 'A product with this name already exists.';
                } else {
                    // If all validations passed, then insert
                    $insertQuery = "INSERT INTO `products` (name, price, image) VALUES (?, ?, ?)";
                    $insertStmt = $conn->prepare($insertQuery);

                    if ($insertStmt === false) {
                        $message[] = "Error preparing insert statement: " . $conn->error;
                    } else {
                        $insertStmt->bind_param("sds", $name, $price, $image_name);

                        if ($insertStmt->execute()) {
                            // Move uploaded file only after successful database insertion
                            if (move_uploaded_file($image_tmp_name, $target_file)) {
                                header("Location: product_list.php"); 
                                exit; 
                            } else {
                                $message[] = "Error moving uploaded image. Check directory permissions.";
                                // If file move fails, delete the partially inserted product record
                                $conn->query("DELETE FROM `products` WHERE id = " . $insertStmt->insert_id);
                            }
                        } else {
                            $message[] = "Error adding product to database: " . $insertStmt->error;
                        }
                        $insertStmt->close();
                    }
                }
                $check_name_stmt->close();
            }
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
    <title>Add Product - MediMax.com</title>
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
           <a href="add_product.php" class="active">Add Product</a>
           <a href="all_orders.php">Orders</a>
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
        echo '<div class="message msg" onclick="this.remove();">'.htmlspecialchars($msg).'</div>';
        }
    }   
?>

        <div class="container" style="backdrop-filter: blur(10px);">
            <h2>Add New Product</h2>
            <form action="add_product.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($name); ?>">
                </div>
                <div class="form-group">
                    <label for="price">Price:</label>
                    <input type="number" name="price" id="price" step="0.01"  value="<?php echo htmlspecialchars($price); ?>">
                </div>
                <div class="form-group">
                    <label for="image">Image:</label>
                    <input type="file" name="image" id="image" accept="image/*">
                </div>
                <button type="submit">Add Product</button>
            </form>
            <a href="product_list.php"><button>Back to Product List</button></a>
        </div>
    </div>

<script src="../../JS/admin.js"></script> </body>
</html>