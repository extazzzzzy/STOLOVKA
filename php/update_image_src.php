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
    $id = $_POST["id"];

    $file_name = $_FILES["image"]["name"];
    $file_tmp = $_FILES["image"]["tmp_name"];
    $file_error = $_FILES["image"]["error"];

    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $allowed_extensions = array("jpg", "jpeg", "png");


    if (in_array($file_ext, $allowed_extensions)) {
        if ($file_error === 0) {
            $path = "/images/products/" . $file_name;
            move_uploaded_file($file_tmp, $path);

            $sql = "UPDATE products SET image_src = '$path' WHERE id = '$id'";
            if ($mysql->query($sql) === TRUE) {
                header('Location: ../Pages/catalog.php');
            } else {
                echo "Ошибка при обновлении пути изображения товара: " . $mysql->error;
            }
        } else {
            echo "Ошибка при загрузке файла: " . $file_error;
        }
    }
    else {
        echo "Допустимые форматы нарушены(.jpg .jpeg .png)";
    }
}


$mysql->close();
?>
