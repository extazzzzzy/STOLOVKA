<?php
session_start();
$user_id = $_SESSION['id'];
$order_address = $_POST['order_add'];
$delivery_time = $_POST['status'];
$comment = $_POST['comment'];

$connectMySQL = new mysqli('localhost', 'root', 'root', 'Stolovka');

// Создание заказа
$connectMySQL->query("INSERT INTO `orders` (`user_id`, `address`, `delivery_time`, `comment`) VALUES ('$user_id', '$order_address', '$delivery_time', '$comment')");
$order_id = $connectMySQL->insert_id;
// Занесение продуктов в заказ
$products_result = $connectMySQL->query("SELECT product_id FROM `carts_to_products` WHERE `user_id` = '$user_id'");
while ($row = $products_result->fetch_assoc()) {
    $product_id = $row['product_id'];
    $connectMySQL->query("INSERT INTO `orders_to_products` (`order_id`, `product_id`) VALUES ('$order_id', '$product_id')");

    // Занесение ингредиентов в заказ
    $ingredients_result = $connectMySQL->query("SELECT ingredient_id FROM `carts_to_products_to_ingredients` WHERE `user_id` = '$user_id' AND `product_id` = '$product_id'");
    while ($row2 = $ingredients_result->fetch_assoc()) {
        $ingredient_id = $row2['ingredient_id'];
        $connectMySQL->query("INSERT INTO `orders_to_products_to_ingredients` (`order_id`, `product_id`, `ingredient_id`) VALUES ('$order_id', '$product_id', '$ingredient_id')");
    }
}

// Перенаправление на другую страницу после создания заказа
header("Location: ../pages/orders.php");
exit();
?>