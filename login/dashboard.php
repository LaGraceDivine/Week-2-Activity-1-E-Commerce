<?php
session_start();
require_once("../settings/core.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=Please login first");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>User Dashboard</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f5f5f5;
      margin: 0;
      padding: 0;
    }
    .dashboard-container {
      width: 600px;
      margin: 80px auto;
      padding: 30px;
      background-color: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
      text-align: center;
    }
    .dashboard-container h1 {
      font-size: 28px;
      color: #333;
    }
    .dashboard-container a {
      display: inline-block;
      margin-top: 20px;
      padding: 12px 25px;
      font-size: 16px;
      color: white;
      background-color: #0b97caff;
      text-decoration: none;
      border-radius: 6px;
    }
    .dashboard-container a:hover {
      background-color: #06aecfff;
    }
  </style>
</head>
<body>

  <div class="dashboard-container">
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
    <p>Anything for you!!!</p>

    <?php if (isAdmin()): ?>
      <p><strong>Admin</strong></p>
      <a href="../admin_panel.php">Go to Admin Panel</a>
      <a href="../admin/category.php">Category Management</a>
    <?php else: ?>
      <p></p>
    <?php endif; ?>

    <a href="../actions/logout.php">Logout</a>
  </div>

</body>
</html>
