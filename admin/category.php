<?php
require_once("../settings/core.php");
if (!isLoggedIn() || !isAdmin()) {
    header("Location: ../login.php?error=Unauthorized");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Category Management</title>
  
  <script src="../js/category.js"></script>
  <style>
    body { font-family: Arial, sans-serif; background: #f5f5f5; }
    .container { width: 600px; margin: 50px auto; padding: 20px; background: #fff; border-radius: 8px; }
    h1 { text-align: center; }
    input, button { padding: 8px; margin: 5px; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    table, th, td { border: 1px solid #ddd; }
    th, td { padding: 10px; text-align: left; }
    button { cursor: pointer; }

    <link rel="stylesheet" href="../css/style.css">
    .container{max-width:1100px;margin:20px auto;padding:10px}
    .grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
    label{display:block;margin-bottom:6px}
    input,select,textarea{width:100%;padding:6px;margin-bottom:8px}
    table{width:100%;border-collapse:collapse}
    th,td{padding:6px;border:1px solid #ddd}
  </style>
</head>
<body>
<div class="container">
  <h1>Manage Categories</h1>
  <form id="addCategoryForm">
    <input type="text" id="categoryName" placeholder="Enter category name" required>
    <button type="submit">Add Category</button>
  </form>

  <table id="categoryTable">
    <thead>
      <tr><th>ID</th><th>Name</th><th>Actions</th></tr>
    </thead>
    <tbody></tbody>
  </table>
</div>
</body>
</html>
