<?php
session_start();
$user_id = $_SESSION['id'];
$product_id = $_POST['product_id'];
$checked_ingredients = json_decode($_POST['checked_ingredients'], true);


$connectMySQL = new mysqli('localhost', 'root', 'root', 'Stolovka');
if ($connectMySQL->query("SELECT * FROM `carts_to_products` WHERE `product_id` = '$product_id'")->num_rows == 0)
{
    $connectMySQL->query("INSERT INTO `carts_to_products` (`user_id`, `product_id`) VALUES ('$user_id', '$product_id')");

    $ingredients = $connectMySQL->query("SELECT ingredient_id, is_included FROM `products_to_ingredients` WHERE `product_id` = '$product_id'");
    while ($row = $ingredients->fetch_assoc())
    {
        $ingredient_id = $row['ingredient_id'];
        if (($row['is_included'] == 1 & !in_array($ingredient_id, $checked_ingredients)) | ($row['is_included'] == 0 & in_array($ingredient_id, $checked_ingredients)))
        {
            $connectMySQL->query("INSERT INTO `carts_to_products_to_ingredients` (`user_id`, `product_id` ,`ingredient_id`) VALUES ('$user_id', '$product_id', '$ingredient_id')");
        }
    }
}
?>