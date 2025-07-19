<?php
// PHP/login_form.php

include 'connection.php';
// Start session here as this page is responsible for setting session variables directly
session_start();

// Initialize $_SESSION['message'] if it doesn't exist, as this page might be the first entry point for messages
if (!isset($_SESSION['message'])) {
    $_SESSION['message'] = [];
}

// Check if form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and escape it
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    // Using md5 for password hashing is generally NOT recommended for production.
    // Consider using password_hash() and password_verify() for stronger security.
    $password = md5($_POST["password"]); 

    $select_query = "SELECT id, name, roll FROM `users` WHERE email = '$email' AND password = '$password'";
    $select_result = mysqli_query($conn, $select_query);

    if (!$select_result) {
        // Handle query failure
        $_SESSION['message'][] = 'Database query failed: ' . mysqli_error($conn);
    } elseif (mysqli_num_rows($select_result) > 0) {
        $row = mysqli_fetch_assoc($select_result);
        
        // Set user ID in session
        $_SESSION["user_id"] = $row['id'];
        
        // Optionally, store more user details in session for immediate use across pages
        $_SESSION['user_name'] = $row['name'];
        $_SESSION['user_role'] = $row['roll'];

        // Redirect based on user role
        if ($row['roll'] == 'admin' || $row['roll'] == 'owner') {
            header("Location: AdminPanel.php"); // Assuming AdminPanel.php is in PHP/
        } else {
            header("Location: ../index.php"); // Redirect to the root index.php
        }
        exit(); // Ensure that the script stops after the redirect
    } else {
        // No user found with the given credentials
        $_SESSION['message'][] = 'Incorrect email or password!';
    }
}

// Close connection
// It's generally better to keep the connection open until the end of the script
// or right before exiting if there are no further DB operations.
// For this script, closing it here is fine if no more DB calls happen after form processing.
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MediMax.com</title>
    <link rel="stylesheet" href="../CSS/Login.css"> </head>
<body>

<?php
// Display messages stored in session, then clear them
if (!empty($_SESSION['message'])) {
    foreach ($_SESSION['message'] as $msg) {
        echo '<div class="message" onclick="this.remove();">' . htmlspecialchars($msg) . '</div>';
    }
    unset($_SESSION['message']); // Clear messages after displaying them
}
?>

<div class="container">
    <h2 class="login">Login</h2>
    <form method="post" id="loginForm" action="login_form.php"> <div class="form-group">
            <input type="text" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            <label for="email">Email</label><br>
        </div>
        <p id="emailError" class="error"></p>        

        <div class="form-group">
            <input type="password" id="password" name="password">
            <label for="password">Password</label>
        </div>
        <p id="passwordError" class="error"></p>

        <p id="forgetPass"><a href="forget_password.php">Forgot Password?</a></p> <input type="submit" value="Login" name="login" id="submitbtn">

        <p>Don't have an account? 
        <a href="register_form.php"> Register</a></p> </form>
</div>
<script src="../JS/login_validation.js"></script> </body>
</html>