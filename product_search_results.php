<?php
session_start();
require_once 'classes/db_connection.php';
require_once 'classes/product_class.php';
require_once 'controllers/product_controller.php';

$search = $_GET['search'] ?? '';
$products = $product->search_products($search);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Results for "<?= htmlspecialchars($search) ?>"</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/product.js" defer></script>
</head>
<body>

<h1>Search Results for "<?= htmlspecialchars($search) ?>"</h1>

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

<p><a href="all_product.php">Back to All Products</a></p>

</body>
</html>
