<?php
session_start();
require_once("../settings/core.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=Please login first");
    exit;
}

// Load products for display
require_once("../classes/db_connection.php");
require_once("../classes/product_class.php");
require_once("../controllers/cart_controller.php");

$product = new ProductClass();
$products = $product->view_all_products();

// Get cart count for display
$cart_controller = new CartController();
$cart_items = $cart_controller->get_user_cart_ctr($_SESSION['user_id']);
$cart_count = $cart_items ? count($cart_items) : 0;
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>User Dashboard</title>
  <link rel="stylesheet" href="../css/style.css">
  <script src="../js/cart.js" defer></script>
  <style>

/* ============= RESET ============= */
body {
    font-family: Arial, sans-serif;
    background-color: #f5f5f5;
    margin: 0;
    padding: 0;
    padding-top: 90px;
}

/* ============= NAVIGATION BAR ============= */
nav {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 1000;

    display: flex;
    justify-content: space-between;
    align-items: center;

    padding: 15px 30px;
    background-color: #0b97ca;
    color: white;
}

/* LEFT SIDE OF NAV (Welcome message) */
.nav-left {
    font-size: 20px;
    font-weight: bold;
    color: white;
}

/* RIGHT SIDE OF NAV (Links & Cart) */
.nav-right {
    display: flex;
    align-items: center;
    gap: 15px;
}

nav a {
    color: white;
    text-decoration: none;
    padding: 8px 15px;
    background-color: transparent;
    border-radius: 6px;
    transition: 0.3s;
    font-size: 15px;
}

nav a:hover {
    background-color: #06aecf;
}

/* SEARCH BAR IN NAV */
nav form {
    display: flex;
    align-items: center;
    gap: 5px;
}

nav form input[type="text"] {
    padding: 6px 10px;
    border-radius: 4px;
    border: none;
    width: 200px;
}

nav form button {
    padding: 6px 10px;
    border: none;
    border-radius: 4px;
    background-color: #06aecf;
    color: white;
    cursor: pointer;
}

nav form button:hover {
    background-color: #0b97ca;
}

/* CART COUNTER */
.cart-link {
    position: relative;
}

#cartCount {
    background: #ef4444;
    color: white;
    border-radius: 50%;
    padding: 2px 6px;
    font-size: 12px;
    position: absolute;
    top: -8px;
    right: -8px;
    min-width: 18px;
    text-align: center;
    line-height: 14px;
}

/* ============= WELCOME BANNER ============= */
.welcome-section {
    background: white;
    padding: 20px 30px;
    margin: 20px auto;
    max-width: 1400px;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.welcome-section h1 {
    margin: 0;
    color: #333;
}

    /* ============= PRODUCTS GRID ============= */
    .products-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 20px;
        padding: 20px;
        max-width: 1400px;
        margin: 0 auto;
    }

    .product {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        padding: 15px;
        text-align: center;
        transition: transform 0.2s;
    }

    .product:hover {
        transform: translateY(-5px);
    }

    .product img {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 6px;
        cursor: pointer;
    }

    .product h3 {
        font-size: 18px;
        margin: 10px 0;
        color: #333;
    }

    .product p {
        margin: 5px 0;
        color: #555;
    }

    .product button {
        padding: 8px 12px;
        background-color: #0b97ca;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        margin-top: 10px;
    }

    .product button:hover {
        background-color: #06aecf;
    }

  </style>
</head>
<body>

  <!-- Navigation Bar -->
  <nav>
    <div>
      <a href="dashboard.php">Dashboard</a>
      <a href="../cart.php" class="cart-link" style="position: relative;">
        🛒 Cart
        <?php if ($cart_count > 0): ?>
        <span id="cartCount" style="background: #ef4444; color: white; border-radius: 50%; padding: 2px 6px; font-size: 12px; position: absolute; top: -8px; right: -8px; min-width: 18px; text-align: center; line-height: 14px;"><?= $cart_count ?></span>
        <?php else: ?>
        <span id="cartCount" style="display: none;">0</span>
        <?php endif; ?>
        <?php if (isAdmin()): ?>
            <a href="../admin/category.php">Category</a>
            <a href="../admin/brand.php">Brand</a>
            <a href="../admin/product.php">Add Product</a>
        <?php endif; ?>
      </a>
    </div>
    </div>

    <form action="../product_search_results.php" method="get">
      <input type="text" name="search" placeholder="Search products...">
      <button type="submit">Search</button>
    </form>

    <div>
      <span style="margin-right: 15px;">Welcome, <?= htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>!</span>
      <a href="../actions/logout.php">Logout</a>
    </div>
  </nav>

  <!-- Products Grid -->
  <div class="products-container" id="productList">
    <?php if (empty($products)): ?>
      <div style="grid-column: 1 / -1; text-align: center; padding: 40px;">
        <p style="font-size: 18px; color: #666;">No products available at the moment.</p>
      </div>
    <?php else: ?>
      <?php foreach($products as $p): ?>
        <div class="product">
          <a href="../single_product.php?id=<?= $p['product_id'] ?>">
            <?php 
            $imgPath = $p['product_image'] ?? '/..uploads/placeholder.jpg';
            if ($imgPath && $imgPath !== 'placeholder.jpg' && strpos($imgPath, 'http') !== 0 && strpos($imgPath, '/') !== 0) {
              $imgPath = '../uploads\u1\p3\image_1763237283_dbab2b31058f.png' . $imgPath;
            }
            ?>
            <img src="<?= htmlspecialchars($imgPath) ?>" 
                 alt="<?= htmlspecialchars($p['product_title'] ?? 'Product') ?>" 
                 onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'150\' height=\'150\'%3E%3Crect fill=\'%23ddd\' width=\'150\' height=\'150\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%23999\'%3ENo Image%3C/text%3E%3C/svg%3E';"
                 style="max-width: 150px; max-height: 150px;">
          </a>
          <h3><?= htmlspecialchars($p['product_title'] ?? 'Product') ?></h3>
          <p>$<?= number_format($p['product_price'] ?? 0, 2) ?></p>
          <p><?= htmlspecialchars($p['category_name'] ?? 'Uncategorized') ?> | <?= htmlspecialchars($p['brand_name'] ?? 'No Brand') ?></p>
          <button onclick="addToCart(<?= $p['product_id'] ?>, 1)" class="add-to-cart-btn">Add to Cart</button>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

</body>
</html>