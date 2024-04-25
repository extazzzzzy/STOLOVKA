<?php

session_start();
try {
    $connectMySQL = new mysqli('localhost', 'root', 'root', 'Stolovka');

    $user_id = $_SESSION['id'];
    $result1 = $connectMySQL->query("SELECT * FROM carts_to_products WHERE user_id = '$user_id'");
    $count = $result1->num_rows;
    echo $count;
}
catch (Exception $e) {
    echo "Ошибка получения данных!";
}
?>