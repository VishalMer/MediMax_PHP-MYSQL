<?php

include 'connection.php';

session_start();

if (!isset($_SESSION['message'])) {
    $_SESSION['message'] = [];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    
    $password = md5($_POST["password"]); 

    $select_query = "SELECT id, name, role FROM `users` WHERE email = '$email' AND password = '$password'";
    $select_result = mysqli_query($conn, $select_query);

    if (!$select_result) {
        
        $_SESSION['message'][] = 'Database query failed: ' . mysqli_error($conn);
    } elseif (mysqli_num_rows($select_result) > 0) {
        $row = mysqli_fetch_assoc($select_result);
        
        
        $_SESSION["user_id"] = $row['id'];
        $_SESSION['user_name'] = $row['name'];
        $_SESSION['user_role'] = $row['roll'];

        if ($row['roll'] == 'admin' || $row['roll'] == 'owner') {
            header("Location: AdminPanel.php"); 
        } else {
            header("Location: ../index.php"); 
        }
        exit();
    } else {
        $_SESSION['message'][] = 'Incorrect email or password!';
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
    <link rel="stylesheet" href="../CSS/Login.css"> </head>
<body>

<?php

if (!empty($_SESSION['message'])) {
    foreach ($_SESSION['message'] as $msg) {
        echo '<div class="message" onclick="this.remove();">' . htmlspecialchars($msg) . '</div>';
    }
    unset($_SESSION['message']); 
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