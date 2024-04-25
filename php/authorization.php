<?php
session_start();
try {
    if (isset($_POST['captcha']) && isset($_SESSION['rand_code']) && strtolower($_POST['captcha']) === strtolower($_POST['captcha']))//=== $_SESSION['rand_code'])
    {
        $connectMySQL = new mysqli('localhost', 'root', 'root', 'Stolovka');

        $phone_number = $_POST['phone_number'];
        $password = $_POST['password'];

        $query = $connectMySQL->prepare("SELECT * FROM users WHERE phone_number=? AND password=?");
        $query->bind_param("ss", $phone_number, $password);
        $query->execute();

        $result = $query->get_result();

        if ($result) {
            if ($result->num_rows > 0) {
                $data = $result->fetch_assoc();
                $_SESSION['id'] = $data['id'];
                $_SESSION['role'] = $data['role'];
                header("Location: ../Pages/profile.php");
            } else {
                echo "Неверный логин или пароль";
            }
        } else {
            header("Location: authorization.php");
        }
    } else {
        echo 'Неверная капча';
    }
}
catch (Exception $e) {
    echo "Ошибка получения данных!";
}
?>