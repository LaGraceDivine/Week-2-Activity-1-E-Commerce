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
            <img src="<?= htmlspecialchars($p['product_image'] ?? 'placeholder.jpg') ?>" alt="<?= htmlspecialchars($p['product_title'] ?? 'Product') ?>" width="150">
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
