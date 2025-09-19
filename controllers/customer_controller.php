<?php
require_once __DIR__ . "/../classes/customer_class.php";

function register_customer_ctr($data) {
    $customer = new Customer();
    return $customer->addCustomer(
        $data['full_name'],
        $data['email'],
        $data['password'],  // raw password
        $data['country'],
        $data['city'],
        $data['contact_number']
    );
}
