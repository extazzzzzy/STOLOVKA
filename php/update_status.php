<?php
try {
    $conn = new mysqli('localhost', 'root', 'root', 'stolovka');

    if ($conn->connect_error) {
        die("Ошибка подключения: " . $conn->connect_error);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $status = $_POST['status'];
        $order_id = $_POST['order_id'];

        if ($status === "В ожидании курьера") {
            $update_sql = "UPDATE orders SET status='$status', deliveryman_id=NULL WHERE id=$order_id";
        } else {
            $update_sql = "UPDATE orders SET status='$status' WHERE id=$order_id";
        }

        if ($conn->query($update_sql) === TRUE) {
            header("Location: ../Pages/orders.php");
        } else {
            echo "Ошибка при обновлении статуса заказа: " . $conn->error;
        }
    }
    $conn->close();
}
catch (Exception $e) {
    echo "Ошибка получения данных!";
}
?>