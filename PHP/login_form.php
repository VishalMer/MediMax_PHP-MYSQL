<?php

include 'connection.php';
session_start();

// Check if form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get form data
    $email = $_POST["email"];
    $password = md5($_POST["password"]);

    $select = mysqli_query($conn, "SELECT * FROM `users` WHERE email = '$email' AND password = '$password' ") or die('Query failed');
    
    // Check if user already exists
    if(mysqli_num_rows($select) > 0){
        $row = mysqli_fetch_assoc($select);
        $_SESSION["user_id"] = $row['id'];
        
        // Check the user role
        $role = $row['roll']; // Assuming 'roll' is the correct column name in your table
        
        if($role == 'admin' || $role == 'owner') {
            header("Location: AdminPanel.php");
        } else {
            header("Location: index.php");
        }
        exit(); // Ensure that the script stops after the redirect
    } else {
        $message[] = 'Incorrect password or email!';
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
    <title>Login - MediMax.com</title>
    <link rel="stylesheet" href="../CSS/Login.css">
</head>
<body>

<?php
if(isset($message)){
    foreach($message as $message){
        echo '<div class="message" onclick="this.remove();">'.$message.'</div>';
    }
}
?>

<div class="container">
    <h2 class="login">Login</h2>
    <form method="post" id="loginForm">
        <div class="form-group">
            <input type="text" id="email" name="email" >
            <label for="email">Email</label><br>
        </div>
        <p id="emailError" class="error"></p>       

        <div class="form-group">
            <input type="password" id="password" name="password" >
            <label for="password">Password</label>
        </div>
        <p id="passwordError" class="error"></p>

        <p id="forgetPass"><a href="#">Forgot Password?</a></p>
        <input type="submit" value="Login" name="login" id="submitbtn">

        <p>Don't have an account? 
        <a href="register_form.php"> Register</a></p>
    </form>
</div>
<script src="../JS/login_validation.js"></script>
</body>
</html>