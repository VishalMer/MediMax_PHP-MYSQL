<?php
// PHP/register_form.php

include 'connection.php';
session_start(); // Start session for message handling

// Initialize $_SESSION['message'] if it doesn't exist
if (!isset($_SESSION['message'])) {
    $_SESSION['message'] = [];
}

// Check if form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and escape it for database insertion
    $name = mysqli_real_escape_string($conn, $_POST["name"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    
    // --- IMPORTANT: Use strong password hashing! ---
    // DO NOT use md5() for production. Use password_hash().
    // Example: $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    // For now, keeping md5() to match your existing code, but strongly recommend changing this.
    $password = md5($_POST["password"]); 

    // Check if user already exists
    $select_query = "SELECT * FROM `users` WHERE email = '$email'";
    $select_result = mysqli_query($conn, $select_query);
    
    if (!$select_result) {
        $_SESSION['message'][] = 'Database query failed: ' . mysqli_error($conn);
    } elseif (mysqli_num_rows($select_result) > 0) {
        $_SESSION['message'][] = 'User already exists with this email!';
    } else {
        // Insert data into database
        // Default role could be 'user' or 'customer' in your database schema
        $sql = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', 'user')"; // Added default 'role'

        if ($conn->query($sql) === TRUE) {
            $_SESSION['message'][] = 'Registered successfully! Now you can Login';
            // Optionally redirect to login page immediately after successful registration
            header('Location: login_form.php');
            exit();
        } else {
            $_SESSION['message'][] = "Error registering user: " . $conn->error;
        }
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - MediMax.com</title>
    <link rel="stylesheet" href="../CSS/Login.css"> </head>
<body>

<?php
// Display messages stored in session, then clear them
if (!empty($_SESSION['message'])) {
    foreach ($_SESSION['message'] as $msg) { // Use $msg to avoid conflict
        echo '<div class="message" onclick="this.remove();">' . htmlspecialchars($msg) . '</div>';
    }
    unset($_SESSION['message']); // Clear messages after displaying them
}
?>

    <div class="container">
    <h2 class="login">Register</h2>
    <form method="post" id="registrationForm" action="register_form.php"> <div class="form-group">
            <input type="text" id="username" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
            <label for="username">Full Name </label><br>
        </div>
        <p id="usernameError" class="error"></p>

        <div class="form-group">
            <input type="text" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            <label for="email">Email </label><br>
        </div>
        <p id="emailError" class="error"></p>

        <div class="form-group">
            <input type="password" id="password" name="password">
            <label for="password">Password </label>
        </div>
        <p id="passwordError" class="error"></p>

        <input type="submit" value="Register" name="register" id="submitbtn">

        <p>Already have an account? 
        <a href="login_form.php"> Login</a></p> </form>
    </div>
    <script src="../JS/register_validation.js"></script> </body>
</html>