<?php
session_start();
$conn = new mysqli('localhost', 'root', 'root', 'stolovka');

if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['take_order'])) {
    $order_id = $_POST['order_id'];
    $userID = $_SESSION['id'];

    $update_sql = "UPDATE orders SET deliveryman_id = $userID, status = 'Утверждение курьера' WHERE id = $order_id";

    if ($conn->query($update_sql) === TRUE) {
        header("Location: ../Pages/orders.php");
    } else {
        echo "Ошибка при взятии заказа: " . $conn->error;
    }
}
$conn->close();
?>