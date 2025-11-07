<?php
require_once __DIR__ . '/../classes/product_class.php';

class ProductController {
    private ProductClass $model;
    public function __construct(){ $this->model = new ProductClass(); }

    public function add_product_ctr($args){ return $this->model->add($args); }
    public function update_product_ctr($id,$args){ return $this->model->update($id,$args); }
    public function get_products_by_user_ctr($uid){ return $this->model->getByUser($uid); }
    public function get_product_ctr($id){ return $this->model->get($id); }
    public function get_all_products_ctr(){ return $this->model->getAllForAdmin(); }
}
