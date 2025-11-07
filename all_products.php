<?php
session_start();
require_once 'db/db_connection.php';
require_once 'classes/product_class.php';
require_once 'controllers/product_controller.php';

$products = $product->view_all_products();
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Products</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/product.js" defer></script>
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
        <a href="single_product.php?id=<?= $p['id'] ?>">
            <img src="product/<?= $p['image'] ?>" alt="<?= $p['title'] ?>" width="150">
        </a>
        <h3><?= $p['title'] ?></h3>
        <p>$<?= $p['price'] ?></p>
        <p><?= $p['category_name'] ?> | <?= $p['brand_name'] ?></p>
        <button>Add to Cart</button>
    </div>
<?php endforeach; ?>
</div>

</body>
</html>
