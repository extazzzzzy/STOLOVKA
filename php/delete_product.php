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

    if (isset($_POST["id"])) {
        $id = $_POST["id"];

        $sql_delete_ingredients = "DELETE FROM products_to_ingredients WHERE product_id = '$id'";
        if (!$mysql->query($sql_delete_ingredients)) {
            echo "Ошибка при удалении связанных ингредиентов: " . $mysql->error;
            exit;
        }

        $sql_delete_product = "DELETE FROM products WHERE id = '$id'";
        if ($mysql->query($sql_delete_product)) {
            header('Location: ../Pages/catalog.php');
        } else {
            echo "Ошибка при удалении продукта: " . $mysql->error;
        }
    }

    $mysql->close();
}
catch (Exception $e) {
    echo "Ошибка получения данных!";
}
?>