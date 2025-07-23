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
    // Example: $password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);
    // For now, keeping md5() to match your existing code, but strongly recommend changing this.
    $password = md5($_POST["password"]);

    $error_found = false; // Flag to indicate if an error has been found

    // --- 1. Check if username (name) already exists first ---
    $select_name_query = "SELECT * FROM `users` WHERE name = '$name'";
    $select_name_result = mysqli_query($conn, $select_name_query);

    if (!$select_name_result) {
        $_SESSION['message'][] = 'Database query failed (username check): ' . mysqli_error($conn);
        $error_found = true;
    } elseif (mysqli_num_rows($select_name_result) > 0) {
        $_SESSION['message'][] = 'The username is already taken!';
        $error_found = true;
    }

    // --- 2. If no username error, then check if email already exists ---
    // This check only runs IF NO USERNAME ERROR WAS FOUND
    if (!$error_found) {
        $select_email_query = "SELECT * FROM `users` WHERE email = '$email'";
        $select_email_result = mysqli_query($conn, $select_email_query);

        if (!$select_email_result) {
            $_SESSION['message'][] = 'Database query failed (email check): ' . mysqli_error($conn);
            $error_found = true;
        } elseif (mysqli_num_rows($select_email_result) > 0) {
            $_SESSION['message'][] = 'User already exists with this email!';
            $error_found = true;
        }
    }

    // --- 3. If no errors (username or email), proceed with insertion ---
    if (!$error_found) { // Only attempt insertion if no error was found in previous checks
        // Insert data into database
        // Default role could be 'user' or 'customer' in your database schema
        $sql = "INSERT INTO `users` (name, email, password, role) VALUES ('$name', '$email', '$password', 'user')"; // Added default 'role'

        if (mysqli_query($conn, $sql)) {
            $_SESSION['message'][] = 'Registered successfully! Now you can Login';
            // Optionally redirect to login page immediately after successful registration
            header('Location: login_form.php');
            exit();
        } else {
            $_SESSION['message'][] = "Error registering user: " . mysqli_error($conn);
        }
    }

    // No need for an array_merge here, as we add messages one by one and stop
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
    <link rel="stylesheet" href="../CSS/Login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
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
    <h2 class="login">Register</h2>
    <form method="post" id="registrationForm" action="register_form.php">
        <div class="form-group">
            <input type="text" id="username" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" >
            <label for="username">Full Name </label><br>
        </div>
        <p id="usernameError" class="error"></p>

        <div class="form-group">
            <input type="text" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" >
            <label for="email">Email </label><br>
        </div>
        <p id="emailError" class="error"></p>

        <div class="form-group">
            <input type="password" id="password" name="password" >
            <label for="password">Password </label>
        </div>
        <p id="passwordError" class="error"></p>

        <input type="submit" value="Register" name="register" id="submitbtn">

        <p>Already have an account?
        <a href="login_form.php"> Login</a></p>
    </form>
    </div>
    <script src="../JS/register_validation.js"></script>
</body>
</html>