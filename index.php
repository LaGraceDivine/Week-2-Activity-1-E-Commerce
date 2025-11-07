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
  <meta charset="UTF-8">
  <title>Home - All Products</title>
  <link rel="stylesheet" href="css/home.css">
  <link rel="stylesheet" href="css/style.css">
  <script src="js/product.js" defer></script>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f5f5f5;
      margin: 0;
      padding: 0;
    }

    /* Top navigation bar */
    nav {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 30px;
      background-color: #0b97caff;
      color: white;
    }

    nav a {
      color: white;
      text-decoration: none;
      margin-left: 15px;
      padding: 8px 15px;
      background-color: #0b97caff;
      border-radius: 6px;
      transition: 0.3s;
    }

    nav a:hover {
      background-color: #06aecfff;
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
      background-color: #06aecfff;
      color: white;
      cursor: pointer;
    }

    nav form button:hover {
      background-color: #0b97caff;
    }

    /* Products grid */
    .products-container {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 20px;
      padding: 30px;
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
      background-color: #0b97caff;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      margin-top: 10px;
    }

    .product button:hover {
      background-color: #06aecfff;
    }
  </style>
</head>
<body>

<!-- Navigation bar -->
<nav>
    <div>
        <a href="login/register.php">Register</a>
        <a href="login/login.php">Login</a>
    </div>

    <form action="product_search_result.php" method="get">
        <input type="text" name="search" placeholder="Search products...">
        <button type="submit">Search</button>
    </form>
</nav>

<!-- Products Grid -->
<div class="products-container" id="productList">
<?php foreach($products as $p): ?>
    <div class="product">
        <a href="single_product.php?id=<?= $p['id'] ?>">
            <img src="product/<?= $p['image'] ?>" alt="<?= $p['title'] ?>">
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
