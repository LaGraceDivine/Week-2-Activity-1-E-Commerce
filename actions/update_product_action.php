<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../controllers/product_controller.php';
require_once __DIR__ . '/../helpers/auth.php';

if (!isloggedIn() || !isAdmin()) { http_response_code(403); echo json_encode(['success'=>false,'error'=>'Unauthorized']); exit; }

$data = json_decode(file_get_contents('php://input'), true);
$product_id = (int)($data['product_id'] ?? 0);
if ($product_id <= 0) { echo json_encode(['success'=>false,'error'=>'Invalid product id']); exit; }

$args = [
    'product_cat' => (int)$data['product_cat'],
    'product_brand' => (int)$data['product_brand'],
    'product_title' => trim($data['product_title']),
    'product_price' => (float)$data['product_price'],
    'product_desc' => trim($data['product_desc'] ?? ''),
    'product_image' => trim($data['product_image'] ?? null),
    'product_keywords' => trim($data['product_keywords'] ?? null)
];

$ctr = new ProductController();
$res = $ctr->update_product_ctr($product_id, $args);
echo json_encode($res);
