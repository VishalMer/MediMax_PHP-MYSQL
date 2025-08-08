<?php

include '../connection.php'; 
include 'admin_details.php'; 

$message = []; 

$productId = '';
$name = '';
$price = '';
$current_image_name = ''; 

if (isset($_GET['id'])) {
    $productId = $_GET['id'];

    $query = "SELECT id, name, price, image FROM `products` WHERE id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die("Error preparing select statement: " . $conn->error);
    }
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        $name = $product['name'];
        $price = $product['price'];
        $current_image_name = $product['image']; 
    } else {
        $message[] = "Product not found!";
    }
    $stmt->close();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = $_POST['id'];
    $name = trim($_POST['name']);
    $price = $_POST['price'];

    $name_errors = [];
    $price_errors = [];
    $image_errors = [];
    $db_errors = []; 

    // Name Validation
    if (empty($name)) {
        $name_errors[] = 'Product name is required.';
    }
    // Price Validation
    if (empty($price)) {
        $price_errors[] = 'Price is required.';
    } elseif (!is_numeric($price) || $price < 0) {
        $price_errors[] = 'Price must be a positive number.';
    }
    
    // Image Validation (only if a new image file is selected)
    if (!empty($_FILES['image']['name'])) {
        $image_name = $_FILES['image']['name'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_size = $_FILES['image']['size'];
        $image_error = $_FILES['image']['error'];

        $target_dir = "../../Images/"; 
        $target_file = $target_dir . basename($image_name);
        $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if ($image_error !== UPLOAD_ERR_OK) {
            switch ($image_error) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $image_errors[] = 'Image file is too large (exceeds server limit).';
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $image_errors[] = 'Image file was only partially uploaded.';
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $image_errors[] = 'No image file was uploaded. Please select an image.';
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $image_errors[] = 'Missing a temporary folder for image uploads.';
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $image_errors[] = 'Failed to write image file to disk.';
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $image_errors[] = 'A PHP extension stopped the image upload.';
                    break;
                default:
                    $image_errors[] = 'Unknown error uploading image: ' . $image_error; 
            }
        } elseif ($image_size > 5000000) { 
            $image_errors[] = 'Image file is too large. Max 5MB.';
        } elseif (!in_array($image_file_type, ['jpg', 'jpeg', 'png', 'gif'])) {
            $image_errors[] = 'Only JPG, JPEG, PNG, & GIF files are allowed for images.';
        }

        $query_old_image = "SELECT image FROM `products` WHERE id = ?";
        $stmt_old_image = $conn->prepare($query_old_image);
        if ($stmt_old_image) {
            $stmt_old_image->bind_param("i", $productId);
            $stmt_old_image->execute();
            $result_old_image = $stmt_old_image->get_result();
            if ($result_old_image->num_rows > 0) {
                $old_product_data = $result_old_image->fetch_assoc();
                $old_image_name = $old_product_data['image'];
            }
            $stmt_old_image->close();
        }
    } else {
        // Fetch current image name to ensure it's kept in DB if not explicitly changed
        $query_current_image = "SELECT image FROM `products` WHERE id = ?";
        $stmt_current_image = $conn->prepare($query_current_image);
        if ($stmt_current_image) {
            $stmt_current_image->bind_param("i", $productId);
            $stmt_current_image->execute();
            $result_current_image = $stmt_current_image->get_result();
            if ($result_current_image->num_rows > 0) {
                $current_product_data = $result_current_image->fetch_assoc();
                $image_name = $current_product_data['image']; 
                $current_image_name = $image_name; 
            }
            $stmt_current_image->close();
        }
    }

    if (!empty($name_errors)) {
        $message[] = $name_errors[0]; 
    } elseif (!empty($price_errors)) {
        $message[] = $price_errors[0];
    } elseif (!empty($image_errors)) {
        $message[] = $image_errors[0];
    } else {
        // Check if product name already exists in the database
        $check_name_query = "SELECT id FROM `products` WHERE name = ? AND id != ?";
        $check_name_stmt = $conn->prepare($check_name_query);
        if ($check_name_stmt === false) {
            $db_errors[] = "Error preparing name check statement: " . $conn->error;
        } else {
            $check_name_stmt->bind_param("si", $name, $productId);
            $check_name_stmt->execute();
            $check_name_stmt->store_result();
            
            if ($check_name_stmt->num_rows > 0) {
                $db_errors[] = 'A product with this name already exists.';
            }
            $check_name_stmt->close();
        }
        
        if (!empty($db_errors)) {
            $message[] = $db_errors[0];
        }
    }

    if (empty($message)) {
        $updateQuery = "UPDATE `products` SET name=?, price=?";
        $bind_types = "sd";
        $bind_params = [&$name, &$price];

        if (!empty($_FILES['image']['name']) && empty($image_errors)) { 
            $updateQuery .= ", image=?";
            $bind_types .= "s";
            $bind_params[] = &$image_name;
        }

        $updateQuery .= " WHERE id=?";
        $bind_types .= "i";
        $bind_params[] = &$productId;

        $updateStmt = $conn->prepare($updateQuery);
        if ($updateStmt === false) {
            $message[] = "Error preparing update statement: " . $conn->error;
        } else {

            call_user_func_array([$updateStmt, 'bind_param'], array_merge([$bind_types], $bind_params));

            if ($updateStmt->execute()) {

                //move image file if a new one is uploaded
                if (!empty($_FILES['image']['name']) && empty($image_errors)) {
                    if (move_uploaded_file($image_tmp_name, $target_file)) {
                        // Delete old image if a new one was uploaded and moved successfully
                        if (!empty($old_image_name) && file_exists($target_dir . $old_image_name)) {
                            unlink($target_dir . $old_image_name);
                        }
                        header("Location: product_list.php"); 
                        exit;
                    } else {
                        $message[] = "Error moving uploaded image. Check directory permissions.";
                    }
                } else {
                    header("Location: product_list.php"); 
                    exit;
                }
            } else {
                $message[] = "Error updating product: " . $updateStmt->error;
            }
            $updateStmt->close();
        }
    }
} else {
    $message[] = "No product ID provided for editing.";
    header("Location: product_list.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../CSS/adminPanel.css"> <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Edit Product - MediMax.com</title> </head>
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
            <h2 style="margin-top: -0.3em;">Edit Product Details</h2>
            <form action="edit_product.php" method="POST" enctype="multipart/form-data"> <input type="hidden" name="id" value="<?php echo htmlspecialchars($productId); ?>"> <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($name); ?>"> </div>
                <div class="form-group">
                    <label for="price">Price:</label>
                    <input type="number" name="price" id="price" value="<?php echo htmlspecialchars($price); ?>" step="0.01" min="0"> </div>
                <div class="form-group">
                    <label for="image">Change Image:</label>
                    <input type="file" name="image" id="image" accept="image/*">
                    <br>
                    <?php if (!empty($current_image_name)): ?>
                        <div class="current-img">
                            <label>Current Image:</label><br>
                            <img src="../../Images/<?php echo htmlspecialchars($current_image_name); ?>" alt="Current Product Image" style="max-width: 150px; max-height: 150px;"> </div>
                    <?php else: ?>
                        <p>No image available.</p>
                    <?php endif; ?>
                </div>
                <button type="submit">Update Product</button>
            </form>
            <a href="product_list.php"><button style="margin-bottom: -0.3em;">Back to Product List</button></a> </div>
    </div>
</body>
</html>