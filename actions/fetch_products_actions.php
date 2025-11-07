<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../controllers/product_controller.php';
require_once __DIR__ . '/../helpers/auth.php';
if (!isloggedIn() || !isAdmin()) { http_response_code(403); echo json_encode(['success'=>false,'error'=>'Unauthorized']); exit; }

$ctr = new ProductController();
$products = $ctr->get_all_products_ctr();
echo json_encode(['success'=>true,'products'=>$products]);
