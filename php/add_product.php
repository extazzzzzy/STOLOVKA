<?php
session_start();

if ($_SESSION['role'] != "manager") {
    header("Location: auth.php");
    die;
}

$connectMySQL = new mysqli('localhost', 'root', 'root', 'stolovka');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $image = $_POST['image'];

    $targetDir = "../images/products/";
    $fileName = basename($_FILES["image"]["name"]);
    $targetFilePath = $targetDir . $fileName;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

    if (!empty($name) && !empty($price) && !empty($fileName)) {
        $allowTypes = array('jpg', 'png', 'jpeg', 'gif');

        if (in_array($fileType, $allowTypes)) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
                $connectMySQL->query("INSERT INTO `products` (`name`, `price`, `image_src`) VALUES ('$name', '$price', '$targetFilePath')");
                header("Location: catalog.php");
            }
        }
    }
}
?>
