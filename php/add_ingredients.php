<?php
const DB_HOST = 'localhost';
const DB_USER = 'root';
const DB_PASSWORD = 'root';
const DB_NAME = 'stolovka';

$mysql = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if ($mysql->connect_errno) {
    exit("Ошибка подключения к базе данных: " . $mysql->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST["product_id"];
    $new_ingredient_name = $_POST["new_ingredient_name"];

    $insertIngredientSql = "INSERT INTO ingredients (name) VALUES ('$new_ingredient_name')";
    if ($mysql->query($insertIngredientSql) === TRUE) {
        $ingredient_id = $mysql->insert_id;
        $insertToProductSql = "INSERT INTO products_to_ingredients (product_id, ingredient_id, is_included) VALUES ('$product_id', '$ingredient_id', '1')";
        if ($mysql->query($insertToProductSql) === TRUE) {
            header('Location: ../Pages/catalog.php');
        } else {
            echo "Ошибка при добавлении ингредиента к продукту: " . $mysql->error;
        }
    }
}
$mysql->close();
?>
