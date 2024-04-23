<?php
const DB_HOST = 'localhost';
const DB_USER = 'root';
const DB_PASSWORD = 'root';
const DB_NAME = 'stolovka';

$mysql = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($mysql->connect_errno) {
    exit("Ошибка подключения к базе данных: " . $mysql->connect_error);
}

if (isset($_POST["id"]) && isset($_POST["image_src"])) {
    $id = $_POST["id"];
    $image_src = $_POST["image_src"];

    $sql = "UPDATE products SET image_src = '$image_src' WHERE id = '$id'";

    if ($mysql->query($sql) === TRUE) {
        header('Location: ../Pages/catalog.php');
    } else {
        echo "Ошибка при обновлении пути изображения товара: " . $mysql->error;
    }
} else {
    echo "Некорректные данные.";
}

$mysql->close();
?>
