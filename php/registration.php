<?php
session_start();
const DB_HOST = 'localhost';
const DB_USER = 'root';
const DB_PASSWORD = '';
const DB_NAME = 'stolovka';

$mysql = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($mysql->connect_error) {
    die("Connection failed: " . $mysql->connect_error);
}

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

    $statement->bind_param("ssssi", $first_name, $phone_number, $password, $address, $role);
    if (!$statement->execute()) {
        die("Ошибка выполнения запроса: " . $statement->error);
    }

    $id = $statement->insert_id;

    $statement->close();
    $mysql->close();

    header('Location: auth.html');
}
