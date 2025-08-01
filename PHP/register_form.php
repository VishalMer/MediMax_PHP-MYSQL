<?php

include 'connection.php';
session_start(); 

if (!isset($_SESSION['message'])) {
    $_SESSION['message'] = [];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = mysqli_real_escape_string($conn, $_POST["name"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $password = md5($_POST["password"]);

    $error_found = false; 

    $select_name_query = "SELECT * FROM `users` WHERE name = '$name'";
    $select_name_result = mysqli_query($conn, $select_name_query);

    if (!$select_name_result) {
        $_SESSION['message'][] = 'Database query failed (username check): ' . mysqli_error($conn);
        $error_found = true;
    } elseif (mysqli_num_rows($select_name_result) > 0) {
        $_SESSION['message'][] = 'The username is already taken!';
        $error_found = true;
    }

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

    
    if (!$error_found) { 
        $sql = "INSERT INTO `users` (name, email, password, role) VALUES ('$name', '$email', '$password', 'user')"; // Added default 'role'

        if (mysqli_query($conn, $sql)) {
            $_SESSION['message'][] = 'Registered successfully! Now you can Login';

            header('Location: login_form.php');
            exit();
        } else {
            $_SESSION['message'][] = "Error registering user: " . mysqli_error($conn);
        }
    }
}

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

if (!empty($_SESSION['message'])) {
    foreach ($_SESSION['message'] as $msg) {
        echo '<div class="message" onclick="this.remove();">' . htmlspecialchars($msg) . '</div>';
    }
    unset($_SESSION['message']); 
}
?>

    <div class="container">
    <h2 class="login">Register</h2>
    <form method="post" id="registrationForm" action="register_form.php">
        <div class="form-group">
            <input type="text" id="username" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" >
            <label for="username">User Name </label><br>
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