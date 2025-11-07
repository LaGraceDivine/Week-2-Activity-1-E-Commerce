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

    /* Navigation bar */
    nav {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 30px;
      background-color: #0b97caff;
      color: white;
    }

    nav a, nav form button {
      color: white;
      text-decoration: none;
      padding: 8px 12px;
      border-radius: 6px;
      margin-left: 10px;
    }

    nav form input[type="text"] {
      padding: 6px 10px;
      border-radius: 4px;
      border: none;
    }

    .dashboard-container {
      width: 600px;
      margin: 30px auto;
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

    .dashboard-container a.button {
      display: inline-block;
      margin-top: 20px;
      padding: 12px 25px;
      font-size: 16px;
      color: white;
      background-color: #0b97caff;
      text-decoration: none;
      border-radius: 6px;
    }

    .dashboard-container a.button:hover {
      background-color: #06aecfff;
    }
  </style>
</head>
<body>

  <!-- Navigation Bar -->
  <nav>
    <div>
      <a href="../index.php">All Products</a>
      <a href="../product_search_result.php">Search Products</a>
    </div>
    <div>
      <span>Welcome, <?= htmlspecialchars($_SESSION['user_name']); ?>!</span>
      <a href="../actions/logout.php">Logout</a>
    </div>
  </nav>

  <!-- Dashboard Content -->
  <div class="dashboard-container">
    <h1>Welcome, <?= htmlspecialchars($_SESSION['user_name']); ?>!</h1>
    <p>Anything for you!!!</p>

    <?php if (isAdmin()): ?>
      <p><strong>Admin Links:</strong></p>
      <a class="button" href="../admin/category.php">Category Management</a>
      <a class="button" href="../admin/brand.php">Brand Management</a>
      <a class="button" href="../admin/product.php">Add Product</a>
    <?php endif; ?>

  </div>

</body>
</html>
