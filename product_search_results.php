<?php
session_start();
require_once 'classes/db_connection.php';
require_once 'classes/product_class.php';
require_once 'controllers/product_controller.php';

$product = new ProductClass();
$search = $_GET['search'] ?? '';
$products = $product->search_products($search);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Results for "<?= htmlspecialchars($search) ?>"</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/product.js" defer></script>
    <script src="js/cart.js" defer></script>
</head>
<body>
<?php
// Only show cart if user is logged in
$showCart = isset($_SESSION['user_id']);
?>
<nav style="background-color: #0b97caff; padding: 15px 30px; margin-bottom: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <a href="login/dashboard.php" style="color: white; text-decoration: none; margin-right: 15px;">Dashboard</a>
            <?php if ($showCart): ?>
            <a href="cart.php" style="color: white; text-decoration: none; margin-right: 15px;">🛒 Cart</a>
            <?php endif; ?>
        </div>
        <div>
            <?php if ($showCart): ?>
            <span style="margin-right: 15px;">Welcome, <?= htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>!</span>
            <a href="actions/logout.php" style="color: white; text-decoration: none;">Logout</a>
            <?php else: ?>
            <a href="login/register.php" style="color: white; text-decoration: none; margin-right: 15px;">Register</a>
            <a href="login/login.php" style="color: white; text-decoration: none;">Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<h1>Search Results for "<?= htmlspecialchars($search) ?>"</h1>

<?php if (empty($products) || count($products) === 0): ?>
    <div style="text-align: center; padding: 40px; background: white; border-radius: 10px; margin: 20px;">
        <p style="font-size: 18px; color: #666;">No products found matching "<?= htmlspecialchars($search) ?>"</p>
        <p><a href="login/dashboard.php" style="color: #0b97caff; text-decoration: none;">← Back to Dashboard</a></p>
    </div>
<?php else: ?>
<div id="productList" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; padding: 30px;">
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
<?php endif; ?>

</body>
</html>
