<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Home</title>
  <link rel="stylesheet" href="css/home.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f5f5f5;
      margin: 0;
      padding: 0;
    }

    .home-container {
      width: 500px;
      margin: 80px auto;
      padding: 30px;
      background-color: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
      text-align: center;
    }

    .home-container h1 {
      font-size: 32px;
      margin-bottom: 30px;
      color: #333;
    }

    .home-container a {
      display: inline-block;
      margin: 10px 20px;
      padding: 12px 25px;
      font-size: 16px;
      color: white;
      background-color: #0b97caff;
      text-decoration: none;
      border-radius: 6px;
      transition: 0.3s;
    }

    .home-container a:hover {
      background-color: #06aecfff;
    }
  </style>
</head>
<body>

  <div class="home-container">
    <h1>Welcome to Our Website!</h1>
    <a href="login/register.php">Register</a>
    <a href="login/login.php">Login</a>
  </div>

</body>
</html>
