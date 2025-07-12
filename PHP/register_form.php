<?php

include 'connection.php';

// Check if form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get form data
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = md5($_POST["password"]);

    $select = mysqli_query($conn, "SELECT * FROM `users` WHERE email = '$email' ") or die('Query failed');
    
    // Check if user already exists
    if(mysqli_num_rows($select) > 0){
        $message[] = 'user already exists!';
    }else{
    // Insert data into database
         $sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')";
         if ($conn->query($sql) === TRUE) {
            $message[] = 'Registered successfully! now you can <a href="login_form.php">Login</a>';
         } else {
         echo "Error: " . $sql . "<br>" . $conn->error;
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
    <h2 class=login>Register</h2>
    <form method="post" id="registrationForm">

        <div class="form-group">
            <input type = "text" id="username" name="name" >
            <label for="username">Full Name </label><br>
        </div>
        <p id="usernameError" class="error"></p>

        <div class="form-group">
            <input type = "text" id="email" name="email" >
            <label for="email">Email </label><br>
        </div>
        <p id="emailError" class="error"></p>

        <div class="form-group">
            <input type = "password" id="password" name="password" >
            <label for="password">Password </label>
        </div>
        <p id="passwordError" class="error"></p>

        <input type="submit" value="Register" name="register" id="submitbtn">

        <p>Already have an account? 
        <a href="login_form.php">  Login</a></p>
    </form>
    </div>
    <script src="../JS/register_validation.js"></script>

</body>
</html>