<?php
require_once __DIR__ . "/../classes/category_class.php";

function add_category_ctr($name, $user_id) {
    $category = new Category();
    return $category->addCategory($name, $user_id);
}

function get_categories_ctr($user_id) {
    $category = new Category();
    return $category->getCategoriesByUser($user_id);
}

function update_category_ctr($id, $newName, $user_id) {
    $category = new Category();
    return $category->updateCategory($id, $newName, $user_id);
}

function delete_category_ctr($id, $user_id) {
    $category = new Category();
    return $category->deleteCategory($id, $user_id);
}
