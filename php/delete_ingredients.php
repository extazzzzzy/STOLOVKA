<?php
const DB_HOST = 'localhost';
const DB_USER = 'root';
const DB_PASSWORD = 'root';
const DB_NAME = 'stolovka';
try {
    $mysql = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if ($mysql->connect_errno) {
        exit("Ошибка подключения к базе данных: " . $mysql->connect_error);
    }

    if (isset($_POST['product_id']) && isset($_POST['ingredient_id'])) {
        $product_id = $_POST['product_id'];
        $ingredient_id = $_POST['ingredient_id'];
        $deleteLinkSql = "DELETE FROM products_to_ingredients WHERE product_id = '$product_id' AND ingredient_id = '$ingredient_id'";
        if ($mysql->query($deleteLinkSql) === TRUE) {
            $deleteIngredientSql = "DELETE FROM ingredients WHERE id = '$ingredient_id'";
            $deleteIngredientSql1 = "DELETE FROM carts_to_products_to_ingredients WHERE ingredient_id = '$ingredient_id'";
            $deleteIngredientSql2 = "DELETE FROM orders_to_products_to_ingredients WHERE ingredient_id = '$ingredient_id'";

            if ($mysql->query($deleteIngredientSql) === TRUE) {
                if ($mysql->query($deleteIngredientSql) === TRUE) {
                    if ($mysql->query($deleteIngredientSql) === TRUE) {
                    }
                    header('Location: ../Pages/catalog.php');
                } else {
                    echo "Ошибка при удалении ингредиента: " . $mysql->error;
                }
            }
        }
    }

    $mysql->close();
}
catch (Exception $e) {
    echo "Ошибка получения данных!";
}
?>
