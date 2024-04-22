<?php
session_start();
const DB_HOST = 'localhost';
const DB_USER = 'root';
const DB_PASSWORD = 'root';
const DB_NAME = 'stolovka';

$mysql = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($mysql->connect_error) {
    die("Connection failed: " . $mysql->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['captcha']) && isset($_SESSION['rand_code']) && $_POST['captcha'] === $_SESSION['rand_code']) {
        $phone_number = $_POST['phone_number'];
        $password = $_POST['password'];

        $statement = $mysql->prepare("SELECT * FROM users WHERE phone_number = ? AND password = ?");
        if (!$statement) {
            die("Ошибка подготовки запроса: " . $mysql->error);
        }
        $statement->bind_param("ss", $phone_number, $password);
        if (!$statement->execute()) {
            die("Ошибка выполнения запроса: " . $statement->error);
        }

        $result = $statement->get_result();
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            $_SESSION['id'] = $user['id'];
            header('Location: ../Pages/profile.php');
            if (!($_SESSION['id'])) {
                echo "Пользователь не найден";
            }
        }
        $statement->close();
    } else {
        echo "Капча введена неверно";
    }
} else {
    echo "Ошибка";
}
?>
