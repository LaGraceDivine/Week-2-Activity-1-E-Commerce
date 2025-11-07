<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../controllers/product_controller.php';
require_once __DIR__ . '/../helpers/auth.php';
if (!isloggedIn() || !isAdmin()) { http_response_code(403); echo json_encode(['success'=>false,'error'=>'Unauthorized']); exit; }

$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
if ($product_id <= 0) { echo json_encode(['success'=>false,'error'=>'Invalid product id']); exit; }

$ctr = new ProductController();
$p = $ctr->get_product_ctr($product_id);
echo json_encode(['success'=>true,'product'=>$p]);
