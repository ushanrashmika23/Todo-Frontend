<?php
require_once "./config/db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";

    $result = mysqli_query($conn, $sql);

    // Check SQL query
    if (!$result) {
        die("SQL Error: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($result) > 0) {
        header("Location: dashboard.php");
        exit();
    } else {
        $message = "Invalid email or password!";
    }
}
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login Form</title>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial,sans-serif;
}

body{
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:linear-gradient(135deg,#4facfe,#00f2fe);
}

.login-container{
    background:#fff;
    padding:40px;
    width:320px;
    border-radius:12px;
    box-shadow:0 8px 20px rgba(0,0,0,.2);
}

.login-container h2{
    text-align:center;
    margin-bottom:25px;
}

.input-group{
    margin-bottom:20px;
}

.input-group label{
    display:block;
    margin-bottom:6px;
}

.input-group input{
    width:100%;
    padding:10px;
    border:1px solid #ccc;
    border-radius:6px;
}

.login-btn{
    width:100%;
    padding:10px;
    border:none;
    border-radius:6px;
    background:#4facfe;
    color:white;
    cursor:pointer;
}

.message{
    margin-top:15px;
    text-align:center;
    color:red;
}
</style>

</head>
<body>

<div class="login-container">

<h2>Login</h2>

<form method="POST">

<div class="input-group">
<label>Email</label>
<input type="email" name="email" required>
</div>

<div class="input-group">
<label>Password</label>
<input type="password" name="password" required>
</div>

<button class="login-btn" type="submit">Login</button>

<?php
if($message!=""){
    echo "<div class='message'>$message</div>";
}
?>

</form>

</div>

</body>
</html>