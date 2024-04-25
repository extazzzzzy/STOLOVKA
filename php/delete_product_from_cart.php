<?php
session_start();
try {
    $user_id = $_SESSION['id'];
    $product_id = $_POST['product_id'];
    $connectMySQL = new mysqli('localhost', 'root', 'root', 'Stolovka');
//создаём заказ
    $connectMySQL->query("DELETE FROM `carts_to_products` WHERE `product_id` = '$product_id'");
    $result = $connectMySQL->query("SELECT * FROM `carts_to_products_to_ingredients` WHERE `user_id` = '$user_id' AND`product_id` = '$product_id'");
    while ($row = $result->fetch_assoc()) {
        $connectMySQL->query("DELETE FROM `carts_to_products_to_ingredients` WHERE `user_id` = '$user_id' AND `product_id` = '$product_id'");
    }
}
catch (Exception $e) {
    echo "Ошибка получения данных!";
}


?>