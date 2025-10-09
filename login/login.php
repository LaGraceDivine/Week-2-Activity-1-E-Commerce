<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link rel="stylesheet" href="css/login.css">
  <style>

    body {
      font-family: Arial, sans-serif;
      background-color: #f5f5f5;
    }

    .login-container {
      width: 400px;
      margin: 60px auto;
      padding: 25px;
      background-color: #fff;
      border: 1px solid #ddd;
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
      text-align: center;
    }

    .login-container h2 {
      margin-bottom: 20px;
      color: #333;
    }

    .login-container input {
      width: 100%;
      padding: 10px;
      margin: 8px 0;
      border: 1px solid #ccc;
      border-radius: 6px;
      box-sizing: border-box;
    }

    .login-container button {
      width: 100%;
      padding: 12px;
      margin-top: 10px;
      border: none;
      border-radius: 6px;
      background-color: #0989f2ff;
      color: white;
      font-size: 16px;
      cursor: pointer;
      transition: 0.3s;
    }

    .login-container button:hover {
      background-color: #0b97caff;
    }

    #message {
      margin-top: 15px;
      font-weight: bold;
      color: red;
    }

    a {
      display: inline-block;
      margin-top: 10px;
      color: #07addfff;
      text-decoration: none;
    }

    a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <div class="login-container">
    <h2>Login</h2>

    <form id="loginForm" action="../actions/login_process.php" method="POST">
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>

    <?php if (isset($_GET['error'])): ?>
      <p id="message" style="color:red;"><?php echo htmlspecialchars($_GET['error']); ?></p>
    <?php elseif (isset($_GET['success'])): ?>
      <p id="message" style="color:green;"><?php echo htmlspecialchars($_GET['success']); ?></p>
    <?php endif; ?>

    <a href="register.php">Don't have an account? Register</a>
  </div>

</body>
</html>