<?php
session_start();
require_once 'classes/db_connection.php';
require_once 'classes/product_class.php';
require_once 'controllers/product_controller.php';

$product = new ProductClass();
$products = $product->view_all_products();
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Products</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/product.js" defer></script>
    <script src="js/cart.js" defer></script>
</head>
<body>
<nav style="background-color: #0b97caff; padding: 15px 30px; margin-bottom: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <a href="index.php" style="color: white; text-decoration: none; margin-right: 15px;">Home</a>
            <a href="cart.php" style="color: white; text-decoration: none; margin-right: 15px;">🛒 Cart</a>
        </div>
        <div>
            <a href="login/register.php" style="color: white; text-decoration: none; margin-right: 15px;">Register</a>
            <a href="login/login.php" style="color: white; text-decoration: none;">Login</a>
        </div>
    </div>
</nav>

<h1>All Products</h1>

<!-- Filters -->
<select id="categoryFilter">
    <option value="">All Categories</option>
    <?php
    $categories = $conn->query("SELECT * FROM categories");
    while($row = $categories->fetch_assoc()){
        echo "<option value='{$row['id']}'>{$row['category_name']}</option>";
    }
    ?>
</select>

<select id="brandFilter">
    <option value="">All Brands</option>
    <?php
    $brands = $conn->query("SELECT * FROM brands");
    while($row = $brands->fetch_assoc()){
        echo "<option value='{$row['id']}'>{$row['brand_name']}</option>";
    }
    ?>
</select>

<div id="productList">
<?php foreach($products as $p): ?>
    <div class="product">
        <a href="single_product.php?id=<?= $p['product_id'] ?>">
            <?php 
            $imgPath = $p['product_image'] ?? 'placeholder.jpg';
            ?>
            <img src="<?= htmlspecialchars($imgPath) ?>" 
                 alt="<?= htmlspecialchars($p['product_title'] ?? 'Product') ?>" 
                 width="150"
                 onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'150\' height=\'150\'%3E%3Crect fill=\'%23ddd\' width=\'150\' height=\'150\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%23999\'%3ENo Image%3C/text%3E%3C/svg%3E';">
        </a>
        <h3><?= htmlspecialchars($p['product_title'] ?? 'Product') ?></h3>
        <p>$<?= number_format($p['product_price'] ?? 0, 2) ?></p>
        <p><?= htmlspecialchars($p['category_name'] ?? 'Uncategorized') ?> | <?= htmlspecialchars($p['brand_name'] ?? 'No Brand') ?></p>
        <button onclick="addToCart(<?= $p['product_id'] ?>, 1)" class="add-to-cart-btn">Add to Cart</button>
    </div>
<?php endforeach; ?>
</div>

</body>
</html>
