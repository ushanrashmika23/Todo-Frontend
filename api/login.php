<?php

session_start();

require_once "config/db.php";


$message = "";


if($_SERVER["REQUEST_METHOD"] == "POST"){


    $email = $_POST['email'];
    $password = $_POST['password'];



    $sql = "SELECT * FROM users WHERE email='$email'";


    $result = mysqli_query($conn,$sql);



    if(!$result){

        die("SQL Error : ".mysqli_error($conn));

    }



    if(mysqli_num_rows($result)>0){


        $user = mysqli_fetch_assoc($result);



        // password check

        if(password_verify($password,$user['password'])){


            $_SESSION['user_id'] = $user['id'];

            $_SESSION['username'] = $user['name'];

            $_SESSION['email'] = $user['email'];



            header("Location: dashboard.php");

            exit();


        }else{


            $message="Incorrect Password";


        }


    }else{


        $message="User not found";


    }



}


?>



<!DOCTYPE html>

<html>

<head>

<title>
ExpenseFlow Login
</title>


<style>

*{

margin:0;
padding:0;
box-sizing:border-box;
font-family:"Segoe UI";

}



body{

height:100vh;

display:flex;

justify-content:center;

align-items:center;

background:
linear-gradient(135deg,#07111f,#1e3a8a,#0f766e);

}



.login-box{


width:380px;

background:rgba(255,255,255,0.15);

backdrop-filter:blur(20px);

padding:40px;

border-radius:25px;

box-shadow:0 15px 40px rgba(0,0,0,.3);

color:white;


}



.login-box h2{

text-align:center;

margin-bottom:30px;


}



.input-box{

margin-bottom:20px;

}



.input-box label{

display:block;

margin-bottom:8px;

}



.input-box input{


width:100%;

padding:12px;

border:none;

outline:none;

border-radius:10px;

background:rgba(255,255,255,0.2);

color:white;


}



input::placeholder{

color:#ddd;

}



button{


width:100%;

padding:12px;

border:none;

border-radius:10px;

background:#5eead4;

color:#07111f;

font-weight:bold;

font-size:16px;

cursor:pointer;


}



button:hover{

opacity:.8;

}



.message{

text-align:center;

margin-top:15px;

color:#fb7185;

}



.signup{

text-align:center;

margin-top:20px;

}



.signup a{

color:#5eead4;

text-decoration:none;

}


</style>


</head>


<body>



<div class="login-box">


<h2>
💰 ExpenseFlow
</h2>



<form method="POST">


<div class="input-box">

<label>Email</label>

<input 
type="email"
name="email"
placeholder="Enter email"
required>

</div>




<div class="input-box">

<label>Password</label>

<input 
type="password"
name="password"
placeholder="Enter password"
required>


</div>



<button type="submit">

Login

</button>



<?php

if($message!=""){

echo "<div class='message'>$message</div>";

}

?>


<div class="signup">

Don't have an account?

<a href="signup.php">
Signup
</a>


</div>



</form>


</div>


</body>

</html>