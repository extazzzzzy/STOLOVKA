<?php
const DB_HOST = 'localhost';
const DB_USER = 'root';
const DB_PASSWORD = 'root';
const DB_NAME = 'stolovka';

$mysql = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($mysql->connect_errno) {
    exit("Ошибка подключения к базе данных: " . $mysql->connect_error);
}

if (isset($_POST["id"]) && isset($_POST["price"])) {
    $id = $_POST["id"];
    $price = $_POST["price"];

    $sql = "UPDATE products SET price = '$price' WHERE id = '$id'";

    if ($mysql->query($sql) === TRUE) {
        header('Location: ../Pages/catalog.php');
    } else {
        echo "Ошибка при обновлении цены товара: " . $mysql->error;
    }
} else {
    echo "Некорректные данные.";
}

$mysql->close();
?>
й