<?php
// PHP/login_form.php

include 'connection.php';

session_start(); // Ensure session is started at the very beginning

// Initialize a variable to hold the message to be displayed on this page
$display_message = [];

// Check for a specific login message from session (set by products_buttons.php)
if (isset($_SESSION['login_message']) && !empty($_SESSION['login_message'])) {
    $display_message[] = $_SESSION['login_message'];
    unset($_SESSION['login_message']); // Clear the message after displaying
}

// Check for general messages from session (e.g., from a previous login attempt)
// This is your existing $_SESSION['message'] array for other types of messages
if (isset($_SESSION['message']) && !empty($_SESSION['message'])) {
    // Merge existing messages into $display_message
    $display_message = array_merge($display_message, $_SESSION['message']);
    unset($_SESSION['message']); // Clear general messages after displaying
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Corrected the typo from 'identifiler' to 'identifier'
    $identifier = mysqli_real_escape_string($conn, $_POST["identifier"]);
    
    $password = md5($_POST["password"]); // Use md5 as per your existing code, but consider stronger hashing like password_hash in production

    $select_query = "SELECT id, name, role FROM `users` WHERE (email = '$identifier' OR name = '$identifier') AND password = '$password'";
    $select_result = mysqli_query($conn, $select_query);

    if (!$select_result) {
        $display_message[] = 'Database query failed: ' . mysqli_error($conn);
    } elseif (mysqli_num_rows($select_result) > 0) {
        $row = mysqli_fetch_assoc($select_result);

        $_SESSION["user_id"] = $row['id'];
        $_SESSION['user_name'] = $row['name'];
        $_SESSION['user_role'] = $row['role']; 

        if ($row['role'] == 'admin' || $row['role'] == 'owner') {
            header("Location: /medimax/PHP/admin/adminPanel.php"); 
        } else {
            header("Location: /medimax/index.php");
        }
        exit();
    } else {
        // Changed the error message to "Wrong credentials!"
        $display_message[] = 'Wrong credentials!'; 
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MediMax.com</title>
    <link rel="stylesheet" href="../CSS/Login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <?php
    // Display all collected messages
    if (!empty($display_message)) {
        foreach ($display_message as $msg) {
            echo '<div class="message" onclick="this.remove();">' . htmlspecialchars($msg) . '</div>';
        }
    }
    ?>

    <div class="container">
        <h2 class="login">Login</h2>
        <form method="post" id="loginForm" action="login_form.php">
            <div class="form-group">
                <input type="text" id="identifier" name="identifier" value="<?php echo isset($_POST['identifier']) ? htmlspecialchars($_POST['identifier']) : ''; ?>">
                <label for="identifier">Username or Email</label><br>
            </div>
            <p id="identifierError" class="error"></p>

            <div class="form-group">
                <input type="password" id="password" name="password">
                <label for="password">Password</label>
            </div>
            <p id="passwordError" class="error"></p>

            <p id="forgetPass"><a href="forget_password.php">Forgot Password?</a></p>
            <input type="submit" value="Login" name="login" id="submitbtn">

            <p>Don't have an account?
            <a href="register_form.php"> Register</a></p>
        </form>
    </div>
    <script src="../JS/login_validation.js"></script>
</body>
</html>