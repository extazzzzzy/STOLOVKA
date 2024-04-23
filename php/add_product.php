<?php
session_start();
$user_id = $_SESSION['id'];
$product_id = $_POST['product_id'];
$checked_ingredients = json_decode($_POST['checked_ingredients'], true);


$connectMySQL = new mysqli('localhost', 'root', 'root', 'Stolovka');
$result = $connectMySQL->query("INSERT INTO `carts_to_products` (`user_id`, `product_id`) VALUES ('$user_id', '$product_id')");
foreach ($checked_ingredients as $ingredient_id)
{
    $result = $connectMySQL->query("INSERT INTO `carts_to_products_to_ingredients` (`user_id`, `product_id` ,`ingredient_id`) VALUES ('$user_id', '$product_id', '$ingredient_id')");
}
?>