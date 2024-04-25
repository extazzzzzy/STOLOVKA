<?php
session_start();
const DB_HOST = 'localhost';
const DB_USER = 'root';
const DB_PASSWORD = 'root';
const DB_NAME = 'stolovka';
try {
    $mysql = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if ($mysql->connect_error) {
        die("Connection failed: " . $mysql->connect_error);
    }

    $phone_number = $_POST['phone_number'];

    $check_statement = $mysql->prepare("SELECT id FROM users WHERE phone_number = ?");
    if (!$check_statement) {
        die("Ошибка подготовки запроса: " . $mysql->error);
    }

    $check_statement->bind_param("s", $phone_number);
    if (!$check_statement->execute()) {
        die("Ошибка выполнения запроса: " . $check_statement->error);
    }

    $check_statement->store_result();

    if ($check_statement->num_rows > 0) {
        echo "Пользователь с таким номером телефона уже существует.";
        exit;
    }

    $check_statement->close();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $first_name = $_POST['first_name'];
        $phone_number = $_POST['phone_number'];
        $password = $_POST['password'];
        $address = $_POST['address'];

        $role = 'user';

        $statement = $mysql->prepare("INSERT INTO users (first_name, phone_number, password, address, role) VALUES (?, ?, ?, ?, ?)");
        if (!$statement) {
            die("Ошибка подготовки запроса: " . $mysql->error);
        }

        $statement->bind_param("sssss", $first_name, $phone_number, $password, $address, $role);
        if (!$statement->execute()) {
            die("Ошибка выполнения запроса: " . $statement->error);
        }

        $id = $statement->insert_id;

        $statement->close();
        $mysql->close();

        header('Location: ../Pages/auth.php');
    }
}
catch (Exception $e) {
    echo "Ошибка получения данных!";
}
