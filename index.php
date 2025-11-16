<?php
session_start();

// If user is logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: login/dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Welcome - E-Commerce Store</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      margin: 0;
      padding: 0;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .welcome-container {
      background: white;
      padding: 50px;
      border-radius: 20px;
      box-shadow: 0 20px 60px rgba(0,0,0,0.3);
      text-align: center;
      max-width: 500px;
    }

    .welcome-container h1 {
      color: #333;
      margin-bottom: 20px;
      font-size: 36px;
    }

    .welcome-container p {
      color: #666;
      margin-bottom: 30px;
      font-size: 18px;
    }

    .btn {
      display: inline-block;
      padding: 15px 30px;
      margin: 10px;
      text-decoration: none;
      border-radius: 8px;
      font-size: 16px;
      font-weight: 600;
      transition: all 0.3s;
    }

    .btn-primary {
      background-color: #0b97caff;
      color: white;
    }

    .btn-primary:hover {
      background-color: #06aecfff;
      transform: translateY(-2px);
    }

    .btn-secondary {
      background-color: #6b7280;
      color: white;
    }

    .btn-secondary:hover {
      background-color: #4b5563;
      transform: translateY(-2px);
    }
  </style>
</head>
<body>

  <div class="welcome-container">
    <h1>Welcome to Our Store</h1>
    <p>Please login or register to browse our products</p>
    <div>
      <a href="login/register.php" class="btn btn-primary">Register</a>
      <a href="login/login.php" class="btn btn-secondary">Login</a>
    </div>
  </div>

</body>
</html>