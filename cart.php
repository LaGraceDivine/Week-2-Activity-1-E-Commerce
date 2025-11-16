<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login/login.php?error=Please login to view your cart');
    exit;
}

require_once(__DIR__ . '/controllers/cart_controller.php');

$c_id = $_SESSION['user_id'];
$controller = new CartController();
$items = $controller->get_user_cart_ctr($c_id);
$total = 0;
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Your Cart</title>
  <link rel="stylesheet" href="css/style.css">
  <script src="js/cart.js" defer></script>
  <style>
    body {
      font-family: Arial, sans-serif;
      max-width: 1200px;
      margin: 0 auto;
      padding: 20px;
      background-color: #f5f5f5;
    }
    
    h1 {
      color: #333;
      margin-bottom: 20px;
    }
    
    table {
      width: 100%;
      border-collapse: collapse;
      background: white;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      margin-bottom: 20px;
    }
    
    th, td {
      padding: 12px;
      border: 1px solid #ddd;
      text-align: left;
    }
    
    th {
      background-color: #0b97caff;
      color: white;
      font-weight: 600;
    }
    
    tr:hover {
      background-color: #f9f9f9;
    }
    
    .cart-image {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 4px;
    }
    
    .quantity-input {
      width: 60px;
      padding: 6px;
      border: 1px solid #ddd;
      border-radius: 4px;
    }
    
    .btn {
      padding: 10px 20px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 14px;
      margin: 5px;
      transition: all 0.3s;
    }
    
    .btn-primary {
      background-color: #0b97caff;
      color: white;
    }
    
    .btn-primary:hover {
      background-color: #06aecfff;
    }
    
    .btn-danger {
      background-color: #ef4444;
      color: white;
    }
    
    .btn-danger:hover {
      background-color: #dc2626;
    }
    
    .btn-secondary {
      background-color: #6b7280;
      color: white;
    }
    
    .btn-secondary:hover {
      background-color: #4b5563;
    }
    
    .total-section {
      background: white;
      padding: 20px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      margin-bottom: 20px;
    }
    
    .total-section h3 {
      font-size: 24px;
      color: #333;
      margin: 0;
    }
    
    .button-group {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }
    
    .empty-cart {
      text-align: center;
      padding: 40px;
      background: white;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .empty-cart a {
      color: #0b97caff;
      text-decoration: none;
      font-weight: 600;
    }
    
    .empty-cart a:hover {
      text-decoration: underline;
    }
    
    .flash-msg {
      position: fixed;
      top: 20px;
      right: 20px;
      background: #10b981;
      color: white;
      padding: 12px 20px;
      border-radius: 6px;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
      z-index: 1000;
      animation: slideIn 0.3s ease;
    }
    
    @keyframes slideIn {
      from {
        transform: translateX(100%);
        opacity: 0;
      }
      to {
        transform: translateX(0);
        opacity: 1;
      }
    }
  </style>
</head>
<body>
  <h1>Your Shopping Cart</h1>
  
  <?php if (!$items || count($items) === 0): ?>
    <div class="empty-cart">
      <p style="font-size: 18px; color: #666; margin-bottom: 20px;">Your cart is empty.</p>
      <a href="login/dashboard.php">Continue Shopping</a>
    </div>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>Image</th>
          <th>Title</th>
          <th>Price</th>
          <th>Quantity</th>
          <th>Subtotal</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($items as $it): 
            $price = floatval($it['product_price'] ?? 0);
            $qty = intval($it['qty']);
            $subtotal = $price * $qty;
            $total += $subtotal;
            $product_id = $it['p_id'] ?? $it['product_id'] ?? 0;
            $image_path = $it['product_image'] ?? 'placeholder.jpg';
        ?>
          <tr>
            <td>
              <?php 
              // Fix image path - ensure it's accessible from root
              if ($image_path && $image_path !== 'placeholder.jpg' && strpos($image_path, 'http') !== 0 && strpos($image_path, '/') !== 0) {
                  // Path is already relative like "uploads/u1/p1/image.jpg" - use as is
              }
              ?>
              <img src="<?= htmlspecialchars($image_path) ?>" 
                   alt="<?= htmlspecialchars($it['product_title'] ?? 'Product') ?>" 
                   class="cart-image"
                   onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'80\' height=\'80\'%3E%3Crect fill=\'%23ddd\' width=\'80\' height=\'80\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%23999\'%3ENo Image%3C/text%3E%3C/svg%3E';">
            </td>
            <td><?= htmlspecialchars($it['product_title'] ?? 'Product') ?></td>
            <td>$<?= number_format($price, 2) ?></td>
            <td>
              <input type="number" 
                     value="<?= $qty ?>" 
                     min="1" 
                     class="quantity-input"
                     onchange="updateQuantity(<?= $product_id ?>, this)">
            </td>
            <td>$<?= number_format($subtotal, 2) ?></td>
            <td>
              <button onclick="removeItem(<?= $product_id ?>)" class="btn btn-danger">Remove</button>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="total-section">
      <h3>Total: $<?= number_format($total, 2) ?></h3>
    </div>

    <div class="button-group">
      <a href="login/dashboard.php" class="btn btn-secondary" style="text-decoration: none; display: inline-block;">Continue Shopping</a>
      <a href="checkout.php" class="btn btn-primary" style="text-decoration: none; display: inline-block;">Proceed to Checkout</a>
      <button onclick="emptyCart()" class="btn btn-danger">Empty Cart</button>
    </div>
  <?php endif; ?>
</body>
</html>