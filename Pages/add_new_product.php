<?php
session_start();
$connectMySQL = new mysqli('localhost', 'root', 'root', 'stolovka');
if ($_SESSION['role'] != "manager"){
    header("Location: auth.php");
    die;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавление продукта</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #634E42;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            max-width: 400px;
            background-color: #F39200;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            overflow: auto;
            max-height: 80vh;
            justify-content: center;
            align-items: center;
        }
        h3 {
            color: #634E42;
            margin-bottom: 10px;
        }
        input[type="text"] {
            color: #F39200;
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 10px;
            background-color: #634E42;
            border-color: #634E42;
            border-radius: 5px;
            border-style: solid;
            outline: none;
            font-size: 16px;
        }
        ::placeholder {
            color: #F39200;
        }
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #634E42;
            color: #F39200;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button {
            background-color: #634E42;
            color: #F39200;
            border: none;
            border-radius: 5px;
            padding: 8px 20px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #F39200;
            color: #AA6304;
        }
        .form-group {
            margin-bottom: 15px;
            color: #634E42;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input[type="text"] {
            width: calc(100% - 50px);
            padding: 8px;
        }
        nav {
            top: 0;
            left: 0;
            right: 0;
            width: auto;
            margin-top: 30px;
            z-index: 1000;
            text-align: center;
        }
        nav a {
            color: #634E42;
            text-decoration: none;
            margin-right: 15px;
            padding: 5px 10px;
            border-radius: 5px;
            background-color: #F39200;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        nav a:hover {
            background-color: #634E42;
            color: #F39200;
        }
    </style>
</head>
<body>
<div class="container">
    <h3>Добавление нового продукта</h3>
    <form action="../php/insert_product.php" method="post">
        <div class="form-group">
            <label for="name">Название продукта:</label>
            <input type="text" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="price">Цена продукта:</label>
            <input type="text" id="price" name="price" required>
        </div>
        <div class="form-group">
            <label for="image_src">Путь к картинке:</label>
            <input type="text" id="image_src" name="image_src" required>
        </div>
        <div class="form-group">
            <button type="submit">Добавить продукт</button>
        </div>
    </form>
    <nav>
        <a href='catalog.php'>Вернуться в каталог</a>
    </nav>
</div>
</body>
</html>
