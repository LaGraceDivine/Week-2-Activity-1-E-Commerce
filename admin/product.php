<?php
session_start();
require_once __DIR__ . '/../classes/db_connection.php';

// ensure only admin can access
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 1) {
    header("Location: /login/login.php");
    exit;
}

// ✅ connect using the same consistent Database class pattern
class ProductPage extends Database {
    public $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function getCategories() {
        $sql = "SELECT id, name FROM categories ORDER BY name";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBrands() {
        $sql = "SELECT brand_id, brand_name FROM brands ORDER BY brand_name";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// ✅ now use it
$page = new ProductPage();
$cats = $page->getCategories();
$brands = $page->getBrands();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Product Management</title>
  <link rel="stylesheet" href="../css/style.css">
  <style>
    .container{max-width:1100px;margin:20px auto;padding:10px}
    .grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
    label{display:block;margin-bottom:6px}
    input,select,textarea{width:100%;padding:6px;margin-bottom:8px}
    table{width:100%;border-collapse:collapse}
    th,td{padding:6px;border:1px solid #ddd}
  </style>
</head>
<body>
  <main class="container">
    <h1>Product Management</h1>

    <section id="product-form-sec">
      <h2>Add / Edit Product</h2>
      <form id="productForm">
        <input type="hidden" id="product_id" name="product_id" value="0">
        <div class="grid">
          <div>
            <label>Category</label>
            <select id="product_cat" name="product_cat" required>
              <option value="">Select category</option>
              <?php foreach ($cats as $c): ?>
                <option value="<?= htmlspecialchars($c['id']) ?>"><?= htmlspecialchars($c['name']) ?></option>
              <?php endforeach; ?>
            </select>

            <label>Brand</label>
            <select id="product_brand" name="product_brand" required>
              <option value="">Select brand</option>
              <?php foreach ($brands as $b): ?>
                <option value="<?= htmlspecialchars($b['brand_id']) ?>"><?= htmlspecialchars($b['brand_name']) ?></option>
              <?php endforeach; ?>
            </select>

            <label>Title</label>
            <input id="product_title" name="product_title" required>

            <label>Price</label>
            <input id="product_price" name="product_price" type="number" step="0.01" required>
          </div>

          <div>
            <label>Description</label>
            <textarea id="product_desc" name="product_desc" rows="6"></textarea>

            <label>Keywords</label>
            <input id="product_keywords" name="product_keywords">

            <label>Image (upload)</label>
            <input id="product_image_file" type="file" accept="image/*">

            <div style="margin-top:8px"><button type="button" id="uploadImageBtn">Upload Image</button></div>

            <div id="uploadedPreview" style="margin-top:8px"></div>
          </div>
        </div>

        <div style="margin-top:12px">
          <button type="submit" id="saveProductBtn">Save Product</button>
          <button type="button" id="resetBtn">Reset</button>
        </div>
      </form>
    </section>

    <section id="product-list" style="margin-top:20px">
      <h2>Products</h2>
      <div id="productsContainer">Loading…</div>
    </section>
  </main>

  <script src="/js/product.js"></script>
</body>
</html>
