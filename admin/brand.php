<?php
session_start();
require_once __DIR__ . '/../classes/db_connection.php';

// Ensure only admin can access
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 1) {
    header("Location: /login/login.php");
    exit;
}

// Connect to DB and fetch categories
class BrandPage extends Database {
    public $conn;

    public function __construct() {
        $this->conn = $this->connect();
    }

    public function getCategories() {
        $sql = "SELECT id, name FROM categories ORDER BY name";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

$page = new BrandPage();
$categories = $page->getCategories();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Brand Management</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../js/brand.js"></script> <!-- connect your existing JS -->
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .container { width: 700px; margin: 50px auto; padding: 20px; background: #fff; border-radius: 8px; }
        h1 { text-align: center; }
        input, select, button { padding: 8px; margin: 5px 0; width: 100%; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        button { cursor: pointer; }
    </style>
</head>
<body>
<div class="container">
    <h1>Manage Brands</h1>

    <!-- Add Brand Form -->
    <form id="addBrandForm">
        <label>Brand Name</label>
        <input type="text" name="brand_name" id="brand_name" required>

        <label>Category</label>
        <select name="category_id" id="category_id" required>
            <option value="">Select Category</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat['id']) ?>"><?= htmlspecialchars($cat['name']) ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Add Brand</button>
    </form>

    <!-- Brands Table -->
    <table id="brandTable">
        <thead>
            <tr><th>ID</th><th>Brand Name</th><th>Category</th><th>Actions</th></tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<script>
$(document).ready(function () {

  function loadBrands() {
function loadBrands() {
  $.ajax({
    url: "../actions/fetch_brand_action.php",
    method: "GET",
    dataType: "json",
    success: function(response) {
        console.log("FETCH RESPONSE:", response);

        if (response.success && response.brands.length > 0) {
            let rows = "";
            response.brands.forEach(function(brand) {
                rows += `
                <tr>
                    <td>${brand.brand_id}</td>
                    <td>${brand.brand_name}</td>
                    <td>${brand.category_name}</td>
                    <td>Buttons here</td>
                </tr>`;
            });
            $("#brandTable tbody").html(rows);
        } else {
            console.warn("NO BRANDS FOUND FOR USER!");
            $("#brandTable tbody").html('<tr><td colspan="4">No brands found</td></tr>');
        }
    },
    error: function(xhr,status,error){
        console.error("FETCH ERROR:", xhr.responseText);
        alert("Fetch Failed");
    }
  });
}

  }

  loadBrands();

  $("#addBrandForm").submit(function (e) {
    e.preventDefault();

    $.ajax({
      type: "POST",
      url: "../actions/add_brand_action.php",
      data: $(this).serialize(),
      dataType: "json",
      success: function (response) {
        console.log("Add response:", response); // Debug log

        alert(response.message);
        $("#addBrandForm")[0].reset();
        loadBrands();
      }
    });
  });

});

</script>

</body>
</html>
