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
      <form id="productForm" onsubmit="return false;">
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

            <label>Image</label>
            <input id="product_image_file" type="file" accept="image/*" required>
            <div id="uploadedPreview" style="margin-top:8px; font-size:12px; color:#666;"></div>
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

  <script>
    // Inline test - this should always show
    console.log('=== INLINE SCRIPT LOADED ===');
    
    // Test if external script loads
    window.addEventListener('load', function() {
      console.log('=== PAGE FULLY LOADED ===');
      
      // Check if product.js loaded by checking for a function it should define
      setTimeout(function() {
        const testEl = document.getElementById('productForm');
        console.log('productForm element found:', !!testEl);
        
        if (!testEl) {
          console.error('ERROR: productForm element not found in DOM!');
        }
      }, 100);
    });
  </script>
  
  <script src="../js/product.js" onerror="console.error('ERROR: Failed to load product.js file!')"></script>
  
  <script>
    // Test after script should have loaded
    setTimeout(function() {
      console.log('=== POST-LOAD CHECK ===');
      const form = document.getElementById('productForm');
      const saveBtn = document.getElementById('saveProductBtn');
      
      if (form && saveBtn) {
        console.log('Elements found, adding manual click handler as backup...');
        
        // Add a simple manual handler as backup
        if (saveBtn) {
          saveBtn.addEventListener('click', function(e) {
          e.preventDefault();
          console.log('MANUAL BACKUP HANDLER: Save button clicked!');
          alert('Save button clicked! Check console for details.');
          
          // Validate required fields
          const product_cat = document.getElementById('product_cat').value;
          const product_brand = document.getElementById('product_brand').value;
          const product_title = document.getElementById('product_title').value;
          const product_price = document.getElementById('product_price').value;
          
          if (!product_cat || !product_brand || !product_title || !product_price) {
            alert('Please fill in all required fields');
            return;
          }
          
          // Use FormData to handle file upload
          const fd = new FormData();
          fd.append('product_cat', product_cat);
          fd.append('product_brand', product_brand);
          fd.append('product_title', product_title);
          fd.append('product_price', product_price);
          fd.append('product_desc', document.getElementById('product_desc').value);
          fd.append('product_keywords', document.getElementById('product_keywords').value);
          
          // Add image file if selected
          const fileInput = document.getElementById('product_image_file');
          if (fileInput && fileInput.files.length > 0) {
            fd.append('image', fileInput.files[0]);
          }
          
          // Try to submit
          const actionUrl = '../actions/add_product_action.php';
          fetch(actionUrl, {
            method: 'POST',
            body: fd  // Don't set Content-Type - browser will set it with boundary
          })
          .then(res => res.text())
          .then(text => {
            console.log('Response:', text);
            try {
              const data = JSON.parse(text);
              if (data.success) {
                alert('Product saved!');
                location.reload();
              } else {
                alert('Error: ' + (data.error || 'Unknown error'));
              }
            } catch(e) {
              console.error('JSON parse error:', e);
              alert('Server response: ' + text.substring(0, 100));
            }
          })
          .catch(err => {
            console.error('Fetch error:', err);
            alert('Error: ' + err.message);
          });
        });
        } else {
          console.error('saveBtn is null, cannot add event listener');
        }
      } else {
        console.error('ERROR: Form or save button not found!');
      }
    }, 500);
  </script>
</body>
</html>
