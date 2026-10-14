<?php
session_start();
include "db.php";

$error = "";

/* Store redirect page */
if(isset($_GET['set_redirect'])){
    $_SESSION['redirect_after_login'] = $_GET['set_redirect'];
}

/* If already logged in */
if(isset($_SESSION['user_id'])){

    if(isset($_SESSION['redirect_after_login'])){
        $redirect = $_SESSION['redirect_after_login'];
        unset($_SESSION['redirect_after_login']);
    } else {
        $redirect = "profile.php";
    }

    header("Location: $redirect");
    exit();
}

/* Login Process */
if(isset($_POST['login'])){

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id,name,password FROM users WHERE email=:email LIMIT 1");
    $stmt->execute(['email'=>$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if($user && password_verify($password,$user['password'])){

        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];

        if(isset($_SESSION['redirect_after_login'])){
            $redirect = $_SESSION['redirect_after_login'];
            unset($_SESSION['redirect_after_login']);
        } else {
            $redirect = "profile.php";
        }

        header("Location: $redirect");
        exit();

    } else {
        $error = "Invalid email or password";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Adora | Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family: "Poppins", Arial, sans-serif;
}

body{
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    background:
        linear-gradient(rgba(255,255,255,0.85), rgba(255,255,255,0.9)),
        url("images/banner/adora3.jpeg") no-repeat center center/cover;
}

/* soft luxury glow */
body::before{
    content:"";
    position:fixed;
    inset:0;
    background:
        radial-gradient(circle at 20% 20%, rgba(212,175,55,0.15), transparent 45%),
        radial-gradient(circle at 80% 80%, rgba(212,175,55,0.12), transparent 45%);
    pointer-events:none;
}

.adora-login{
    width:100%;
    max-width:400px;
    padding:45px 35px;
    border-radius:24px;
    background: rgba(255,255,255,0.85);
    backdrop-filter: blur(16px);
    border:1px solid rgba(212,175,55,0.35);
    box-shadow: 0 20px 50px rgba(0,0,0,0.15);
    animation: fadeUp 1s ease;
}

.logo{
    text-align:center;
    font-size:34px;
    letter-spacing:4px;
    margin-bottom:10px;
    font-weight:300;
    color:#111;
}
.logo span{
    color:#d4af37;
    font-weight:600;
}

.tagline{
    text-align:center;
    font-size:12px;
    letter-spacing:3px;
    color:#777;
    margin-bottom:35px;
}

.input-group{
    position:relative;
    margin-bottom:28px;
}

.input-group input{
    width:100%;
    padding:14px 12px;
    background:transparent;
    border:none;
    border-bottom:1px solid #ccc;
    color:#111;
    font-size:14px;
    outline:none;
}

.input-group label{
    position:absolute;
    left:12px;
    top:50%;
    transform:translateY(-50%);
    color:#999;
    font-size:13px;
    pointer-events:none;
    transition:0.3s ease;
}

.input-group input:focus + label,
.input-group input:valid + label{
    top:-8px;
    font-size:11px;
    color:#d4af37;
}

/* Button */
.login-btn{
    width:100%;
    padding:14px;
    margin-top:15px;
    border-radius:30px;
    border:none;
    cursor:pointer;
    font-size:14px;
    letter-spacing:2px;
    color:#000;
    background: linear-gradient(135deg, #d4af37, #f7e27c);
    box-shadow: 0 10px 25px rgba(212,175,55,0.45);
    transition:0.4s ease;
}

.login-btn:hover{
    box-shadow: 0 15px 40px rgba(212,175,55,0.7);
    transform: translateY(-2px);
}

.links{
    margin-top:28px;
    text-align:center;
    font-size:12px;
    color:#666;
}

.links a{
    color:#b8962e;
    text-decoration:none;
    margin:0 8px;
    font-weight:500;
}

.links a:hover{
    text-decoration:underline;
}

.error{
    background:#ffe6e6;
    color:#b10000;
    padding:10px;
    border-radius:8px;
    margin-bottom:15px;
    font-size:13px;
    text-align:center;
}

@keyframes fadeUp{
    from{
        opacity:0;
        transform: translateY(30px);
    }
    to{
        opacity:1;
        transform: translateY(0);
    }
}
</style>
</head>
<body>

<div class="adora-login">
    <div class="logo">AD<span>ORA</span></div>
    <div class="tagline">TIMELESS LUXURY</div>

    <form method="POST">
        <?php if($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="input-group">
            <input type="email" name="email" required>
            <label>Email Address</label>
        </div>

        <div class="input-group">
            <input type="password" name="password" required>
            <label>Password</label>
        </div>

        <button class="login-btn" type="submit" name="login">LOGIN</button>

        <div class="links">
            <a href="forgot_password.php">Forgot Password?</a> • 
            <a href="signin.php">Create Account</a>
        </div>
    </form>
</div>

</body>
</html>
