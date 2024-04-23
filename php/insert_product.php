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
    $name = $_POST["name"];
    $price = $_POST["price"];
    $image_src = $_POST["image_src"];

    $sql = "INSERT INTO products (name, price, image_src) VALUES ('$name', '$price', '$image_src')";

    if ($mysql->query($sql) === TRUE) {
        header('Location: ../Pages/catalog.php');
    } else {
        echo "Ошибка при добавлении нового товара: " . $mysql->error;
    }
}
$mysql->close();
?>
