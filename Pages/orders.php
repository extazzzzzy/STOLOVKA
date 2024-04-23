<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заказы</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #e0e0e0;
        }
    </style>
</head>
<body>
<header>
    <?php
    echo "<div><a href='profile.php'>Профиль</a></div>";
    echo "<div><a href='orders.php'>Заказы</a></div>";

    if ($_SESSION['role'] == "user") {
        echo "<div><a href='catalog.php'>Каталог</a></div>";
        echo "<div><a href='cart.php'>Корзина</a></div>";
    }
    else if ($_SESSION['role'] == "manager") {
        echo "<div><a href='catalog.php'>Каталог</a></div>";
    }
    ?>
</header>
<?php
if ($_SESSION['id'] == ''): ?>
    <meta http-equiv="refresh" content="0; url=../Pages/auth.php"/>



<?php elseif ($_SESSION['role'] == 'user'): ?>
    <?php
    try {
        $conn = new mysqli('localhost', 'root', 'root', 'stolovka');

        if ($conn->connect_error) {
            die("Ошибка подключения: " . $conn->connect_error);
        }

        $userID = $_SESSION['id'];
        $sql = "SELECT o.id, o.address, o.status, o.delivery_time, o.order_time, o.comment,
               (SELECT first_name FROM users WHERE id = o.deliveryman_id) AS deliveryman_first_name, 
               (SELECT phone_number FROM users WHERE id = o.deliveryman_id) AS deliveryman_phone_number, 
               GROUP_CONCAT(p.name SEPARATOR '<br>') AS dishes, 
               SUM(p.price) AS total_price
        FROM orders o
        LEFT JOIN orders_to_products otp ON o.id = otp.order_id
        LEFT JOIN products p ON otp.product_id = p.id
        WHERE o.user_id = $userID
        GROUP BY o.id
        ORDER BY o.order_time DESC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo '<div class="table_orders">';
            echo '<table>';
            echo '<thead>';
            echo '<tr>';
            echo '<th>'."№".'</th>';
            echo '<th>'."Адрес доставки".'</th>';
            echo '<th>'."Статус заказа".'</th>';
            echo '<th>'."Дата и время доставки".'</th>';
            echo '<th>'."Заказанные блюда".'</th>';
            echo '<th>'."Комментарий".'</th>';
            echo '<th>'."Общая сумма заказа".'</th>';
            echo '<th>'."Имя курьера".'</th>';
            echo '<th>'."Номер телефона курьера".'</th>';
            echo '<tr>';
            echo '</thead>';
            echo '<tbody>';
            while($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>'.$row['id'].'</td>';
                echo '<td>'.$row['address'].'</td>';
                echo '<td>'.$row['status'].'</td>';
                echo '<td>'.$row['delivery_time'].'</td>';
                echo '<td>';

                $order_id = $row['id'];
                $dishes = explode("<br>", $row['dishes']);
                for ($i = 0; $i < count($dishes); $i++)
                {
                    echo $dishes[$i];
                    $dish_name = $dishes[$i];
                    $dish_id = $conn->query("SELECT id FROM `products` WHERE `name` = '$dish_name'")->fetch_assoc()['id'];
                    $user_ingredients_result = $conn->query("SELECT ingredient_id FROM `orders_to_products_to_ingredients` WHERE `order_id` = '$order_id' AND `product_id` = '$dish_id'");
                    if ($user_ingredients_result->num_rows > 0)
                    {
                        echo '<br>';
                    }
                    while ($row2 = $user_ingredients_result->fetch_assoc())
                    {
                        $ingredient_id = $row2['ingredient_id'];
                        $ingredient_name= $conn->query("SELECT name FROM `ingredients` WHERE `id` = '$ingredient_id'")->fetch_assoc()['name'];
                        if ($conn->query("SELECT is_included FROM `products_to_ingredients` WHERE `product_id` = '$dish_id' AND `ingredient_id` = '$ingredient_id'")->fetch_assoc()['is_included'] == 1)
                        {
                            echo 'Убрать: ' . $ingredient_name;
                        }
                        else
                        {
                            echo 'Добавить: ' . $ingredient_name;
                        }
                    }
                    if ($i < count($dishes) - 1)
                    {
                        echo '<br>';
                    }
                    echo '<br>';
                }
                echo '</td>';
                echo '<td>'.$row['comment'].'</td>';
                echo '<td>'.$row['total_price']." RUB".'</td>';
                echo '<td>'.$row['deliveryman_first_name'].'</td>';
                echo '<td>'.$row['deliveryman_phone_number'].'</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        } else {
            echo '<p>Заказов не найдено</p>';
        }
        $conn->close();
    }
    catch(Exception $e) {
        header("Location: orders.php");
    }

    ?>




<?php elseif ($_SESSION['role'] == 'manager'): ?>
    <?php
    try {
        $conn = new mysqli('localhost', 'root', 'root', 'stolovka');

        if ($conn->connect_error) {
            die("Ошибка подключения: " . $conn->connect_error);
        }

        $userID = $_SESSION['id'];
        $sql = "SELECT o.id, o.address, o.status, o.delivery_time, o.order_time, o.comment, u.phone_number, u.first_name, 
                      (SELECT first_name FROM users WHERE id = o.deliveryman_id) AS deliveryman_first_name, 
               (SELECT phone_number FROM users WHERE id = o.deliveryman_id) AS deliveryman_phone_number, 
       GROUP_CONCAT(p.name SEPARATOR '<br>') AS dishes, SUM(p.price) AS total_price
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        LEFT JOIN orders_to_products otp ON o.id = otp.order_id
        LEFT JOIN products p ON otp.product_id = p.id
        GROUP BY o.id
        ORDER BY o.order_time DESC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo '<div class="table_orders">';
            echo '<table>';
            echo '<thead>';
            echo '<tr>';
            echo '<th>'."№".'</th>';
            echo '<th>'."Имя клиента".'</th>';
            echo '<th>'."Номер телефона".'</th>';
            echo '<th>'."Адрес доставки".'</th>';
            echo '<th>'."Статус заказа".'</th>';
            echo '<th>'."Дата и время доставки".'</th>';
            echo '<th>'."Время заказа".'</th>';
            echo '<th>'."Заказанные блюда".'</th>';
            echo '<th>'."Комментарий".'</th>';
            echo '<th>'."Общая сумма заказа".'</th>';
            echo '<th>'."Имя курьера".'</th>';
            echo '<th>'."Номер телефона курьера".'</th>';
            echo '<tr>';
            echo '</thead>';
            echo '<tbody>';
            while($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>'.$row['id'].'</td>';
                echo '<td>'.$row['first_name'].'</td>';
                echo '<td>'.$row['phone_number'].'</td>';
                echo '<td>'.$row['address'].'</td>';
                if ($row['status'] === 'Доставлен' || $row['status'] === 'Заказ отменен') {
                    echo '<td>'.$row['status'].'</td>';
                }
                else {
                    echo '<td>';
                    echo '<form method="post" action="../php/update_status.php">';
                    echo '<select name="status">';
                    echo '<option value="Ожидает рассмотрения" '.($row['status'] == 'Ожидает рассмотрения' ? 'selected' : '').'>Ожидает рассмотрения</option>';
                    echo '<option value="Принят" '.($row['status'] == 'Принят' ? 'selected' : '').'>Принят</option>';
                    echo '<option value="На кухне" '.($row['status'] == 'На кухне' ? 'selected' : '').'>На кухне</option>';
                    echo '<option value="В ожидании курьера" '.($row['status'] == 'В ожидании курьера' ? 'selected' : '').'>В ожидании курьера</option>';
                    echo '<option value="Утверждение курьера" '.($row['status'] == 'Утверждение курьера' ? 'selected' : '').'>Утверждение курьера</option>';
                    echo '<option value="В доставке" '.($row['status'] == 'В доставке' ? 'selected' : '').'>В доставке</option>';
                    echo '<option value="Проблема с покупателем!" '.($row['status'] == 'Проблема с покупателем!' ? 'selected' : '').'>Проблема с покупателем!</option>';
                    echo '<option value="Заказ отменен" '.($row['status'] == 'Заказ отменен' ? 'selected' : '').'>Заказ отменен</option>';
                    echo '</select>';
                    echo '<input type="hidden" name="order_id" value="'.$row['id'].'">';
                    echo '<input type="submit" name="submit" value="Сохранить">';
                    echo '</form>';
                    echo '</td>';
                }
                echo '<td>'.$row['delivery_time'].'</td>';
                echo '<td>'.$row['order_time'].'</td>';
                echo '<td>';

                $order_id = $row['id'];
                $dishes = explode("<br>", $row['dishes']);
                for ($i = 0; $i < count($dishes); $i++)
                {
                    echo $dishes[$i];
                    $dish_name = $dishes[$i];
                    $dish_id = $conn->query("SELECT id FROM `products` WHERE `name` = '$dish_name'")->fetch_assoc()['id'];
                    $user_ingredients_result = $conn->query("SELECT ingredient_id FROM `orders_to_products_to_ingredients` WHERE `order_id` = '$order_id' AND `product_id` = '$dish_id'");
                    if ($user_ingredients_result->num_rows > 0)
                    {
                        echo '<br>';
                    }
                    while ($row2 = $user_ingredients_result->fetch_assoc())
                    {
                        $ingredient_id = $row2['ingredient_id'];
                        $ingredient_name= $conn->query("SELECT name FROM `ingredients` WHERE `id` = '$ingredient_id'")->fetch_assoc()['name'];
                        if ($conn->query("SELECT is_included FROM `products_to_ingredients` WHERE `product_id` = '$dish_id' AND `ingredient_id` = '$ingredient_id'")->fetch_assoc()['is_included'] == 1)
                        {
                            echo 'Убрать: ' . $ingredient_name;
                        }
                        else
                        {
                            echo 'Добавить: ' . $ingredient_name;
                        }
                    }
                    if ($i < count($dishes) - 1)
                    {
                        echo '<br>';
                    }
                    echo '<br>';
                }
                echo '</td>';
                echo '<td>'.$row['comment'].'</td>';
                echo '<td>'.$row['total_price']." RUB".'</td>';
                echo '<td>'.$row['deliveryman_first_name'].'</td>';
                echo '<td>'.$row['deliveryman_phone_number'].'</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        } else {
            echo '<p>Заказов не найдено</p>';
        }
        $conn->close();
    }
    catch (Exception $e) {
        header("Location: orders.php");
    }
    ?>





<?php elseif ($_SESSION['role'] == 'deliveryman'): ?>
    <?php
    try {
        $conn = new mysqli('localhost', 'root', 'root', 'stolovka');

        if ($conn->connect_error) {
            die("Ошибка подключения: " . $conn->connect_error);
        }

        $userID = $_SESSION['id'];
        $sql = "SELECT o.id, o.address, o.status, o.delivery_time, o.order_time, o.comment, u.phone_number, u.first_name,
       (SELECT first_name FROM users WHERE id = o.deliveryman_id) AS deliveryman_first_name, 
               (SELECT phone_number FROM users WHERE id = o.deliveryman_id) AS deliveryman_phone_number,
               GROUP_CONCAT(p.name SEPARATOR '<br>') AS dishes, SUM(p.price) AS total_price
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        LEFT JOIN orders_to_products otp ON o.id = otp.order_id
        LEFT JOIN products p ON otp.product_id = p.id
        WHERE (o.deliveryman_id IS NULL OR o.deliveryman_id = $userID)
          AND o.status IN ('На кухне', 'В ожидании курьера', 'В доставке', 'Утверждение курьера', 'Проблема с покупателем!', 'Доставлен')
        GROUP BY o.id
        ORDER BY o.order_time DESC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo '<div class="table_orders">';
            echo '<table>';
            echo '<thead>';
            echo '<tr>';
            echo '<th>'."№".'</th>';
            echo '<th>'."Имя клиента".'</th>';
            echo '<th>'."Номер телефона".'</th>';
            echo '<th>'."Адрес доставки".'</th>';
            echo '<th>'."Статус заказа".'</th>';
            echo '<th>'."Дата и время доставки".'</th>';
            echo '<th>'."Время заказа".'</th>';
            echo '<th>'."Заказанные блюда".'</th>';
            echo '<th>'."Комментарий".'</th>';
            echo '<th>'."Общая сумма заказа".'</th>';
            echo '<th>'."Имя курьера".'</th>';
            echo '<th>'."Номер телефона курьера".'</th>';
            echo '<tr>';
            echo '</thead>';
            echo '<tbody>';
            while($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>'.$row['id'].'</td>';
                echo '<td>'.$row['first_name'].'</td>';
                echo '<td>'.$row['phone_number'].'</td>';
                echo '<td>'.$row['address'].'</td>';

                if ($row['status'] === 'В ожидании курьера') {
                    echo '<td>';
                    echo '<form method="post" action="../php/take_order.php">';
                    echo '<input type="hidden" name="order_id" value="'.$row['id'].'">';
                    echo '<input type="submit" name="take_order" value="Взять заказ">';
                    echo '</form>';
                    echo '</td>';
                }
                else if ($row['status'] === 'В доставке') {
                    echo '<td>';
                    echo '<form method="post" action="../php/update_status.php">';
                    echo '<select name="status">';
                    echo '<option value="В доставке" '.($row['status'] == 'В доставке' ? 'selected' : '').'>В доставке</option>';
                    echo '<option value="Доставлен" '.($row['status'] == 'Доставлен' ? 'selected' : '').'>Доставлен</option>';
                    echo '</select>';
                    echo '<input type="hidden" name="order_id" value="'.$row['id'].'">';
                    echo '<input type="submit" name="submit" value="Сохранить">';
                    echo '</form>';

                    echo '<form method="post" action="../php/update_status.php">';
                    echo '<input type="hidden" name="order_id" value="'.$row['id'].'">';
                    echo '<input type="hidden" name="status" value="Проблема с покупателем!">';
                    echo '<input type="submit" name="submit" value="Проблема с покупателем!">';
                    echo '</form>';
                    echo '</td>';
                }
                else if ($row['status'] === 'Проблема с покупателем!') {
                    echo '<td>';
                    echo '<form method="post" action="../php/update_status.php">';
                    echo '<select name="status">';
                    echo '<option value="В доставке" '.($row['status'] == 'В доставке' ? 'selected' : '').'>В доставке</option>';
                    echo '<option value="Доставлен" '.($row['status'] == 'Доставлен' ? 'selected' : '').'>Доставлен</option>';
                    echo '<option value="Проблема с покупателем!" '.($row['status'] == 'Проблема с покупателем!' ? 'selected' : '').'>Проблема с покупателем!</option>';
                    echo '</select>';
                    echo '<input type="hidden" name="order_id" value="'.$row['id'].'">';
                    echo '<input type="submit" name="submit" value="Сохранить">';
                    echo '</form>';
                    echo '</td>';
                }
                else {
                    echo '<td>'.$row['status'].'</td>';
                }

                echo '<td>'.$row['delivery_time'].'</td>';
                echo '<td>'.$row['order_time'].'</td>';
                echo '<td>';

                $order_id = $row['id'];
                $dishes = explode("<br>", $row['dishes']);
                for ($i = 0; $i < count($dishes); $i++)
                {
                    echo $dishes[$i];
                    $dish_name = $dishes[$i];
                    $dish_id = $conn->query("SELECT id FROM `products` WHERE `name` = '$dish_name'")->fetch_assoc()['id'];
                    $user_ingredients_result = $conn->query("SELECT ingredient_id FROM `orders_to_products_to_ingredients` WHERE `order_id` = '$order_id' AND `product_id` = '$dish_id'");
                    if ($user_ingredients_result->num_rows > 0)
                    {
                        echo '<br>';
                    }
                    while ($row2 = $user_ingredients_result->fetch_assoc())
                    {
                        $ingredient_id = $row2['ingredient_id'];
                        $ingredient_name= $conn->query("SELECT name FROM `ingredients` WHERE `id` = '$ingredient_id'")->fetch_assoc()['name'];
                        if ($conn->query("SELECT is_included FROM `products_to_ingredients` WHERE `product_id` = '$dish_id' AND `ingredient_id` = '$ingredient_id'")->fetch_assoc()['is_included'] == 1)
                        {
                            echo 'Убрать: ' . $ingredient_name;
                        }
                        else
                        {
                            echo 'Добавить: ' . $ingredient_name;
                        }
                    }
                    if ($i < count($dishes) - 1)
                    {
                        echo '<br>';
                    }
                    echo '<br>';
                }
                echo '</td>';
                echo '<td>'.$row['comment'].'</td>';
                echo '<td>'.$row['total_price']." RUB".'</td>';
                echo '<td>'.$row['deliveryman_first_name'].'</td>';
                echo '<td>'.$row['deliveryman_phone_number'].'</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        } else {
            echo '<p>Заказов не найдено</p>';
        }
        $conn->close();
    }
    catch (Exception $e) {
        header("Location: orders.php");
    }

    ?>






<?php elseif ($_SESSION['role'] == 'cook'): ?>
    <?php
    try {
        $conn = new mysqli('localhost', 'root', 'root', 'stolovka');

        if ($conn->connect_error) {
            die("Ошибка подключения: " . $conn->connect_error);
        }

        $userID = $_SESSION['id'];
        $sql = "SELECT o.id, o.address, o.status, o.delivery_time, o.order_time, o.comment, u.phone_number, u.first_name,
              (SELECT first_name FROM users WHERE id = o.deliveryman_id) AS deliveryman_first_name, 
               (SELECT phone_number FROM users WHERE id = o.deliveryman_id) AS deliveryman_phone_number,
       GROUP_CONCAT(p.name SEPARATOR '<br>') AS dishes, SUM(p.price) AS total_price
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        LEFT JOIN orders_to_products otp ON o.id = otp.order_id
        LEFT JOIN products p ON otp.product_id = p.id
        WHERE o.status IN ('Принят', 'На кухне', 'В ожидании курьера', 'В доставке', 'Утверждение курьера')
        GROUP BY o.id
        ORDER BY o.order_time DESC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo '<div class="table_orders">';
            echo '<table>';
            echo '<thead>';
            echo '<tr>';
            echo '<th>'."№".'</th>';
            echo '<th>'."Время заказа".'</th>';
            echo '<th>'."Заказанные блюда".'</th>';
            echo '<th>'."Комментарий".'</th>';
            echo '<th>'."Статус заказа".'</th>';
            echo '<th>'."Дата и время доставки".'</th>';
            echo '<th>'."Имя курьера".'</th>';
            echo '<th>'."Номер телефона курьера".'</th>';
            echo '<tr>';
            echo '</thead>';
            echo '<tbody>';
            while($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>'.$row['id'].'</td>';
                echo '<td>'.$row['order_time'].'</td>';
                echo '<td>';

                $order_id = $row['id'];
                $dishes = explode("<br>", $row['dishes']);
                for ($i = 0; $i < count($dishes); $i++)
                {
                    echo $dishes[$i];
                    $dish_name = $dishes[$i];
                    $dish_id = $conn->query("SELECT id FROM `products` WHERE `name` = '$dish_name'")->fetch_assoc()['id'];
                    $user_ingredients_result = $conn->query("SELECT ingredient_id FROM `orders_to_products_to_ingredients` WHERE `order_id` = '$order_id' AND `product_id` = '$dish_id'");
                    if ($user_ingredients_result->num_rows > 0)
                    {
                        echo '<br>';
                    }
                    while ($row2 = $user_ingredients_result->fetch_assoc())
                    {
                        $ingredient_id = $row2['ingredient_id'];
                        $ingredient_name= $conn->query("SELECT name FROM `ingredients` WHERE `id` = '$ingredient_id'")->fetch_assoc()['name'];
                        if ($conn->query("SELECT is_included FROM `products_to_ingredients` WHERE `product_id` = '$dish_id' AND `ingredient_id` = '$ingredient_id'")->fetch_assoc()['is_included'] == 1)
                        {
                            echo 'Убрать: ' . $ingredient_name;
                        }
                        else
                        {
                            echo 'Добавить: ' . $ingredient_name;
                        }
                    }
                    if ($i < count($dishes) - 1)
                    {
                        echo '<br>';
                    }
                    echo '<br>';
                }
                echo '</td>';


                if ($row['status'] !== 'Утверждение курьера' && $row['status'] !== 'В доставке') {
                    echo '<td>';
                    echo '<form method="post" action="../php/update_status.php">';
                    echo '<select name="status">';
                    echo '<option value="Принят" ' . ($row['status'] == 'Принят' ? 'selected' : '') . '>Принят</option>';
                    echo '<option value="На кухне" ' . ($row['status'] == 'На кухне' ? 'selected' : '') . '>На кухне</option>';
                    echo '<option value="В ожидании курьера" ' . ($row['status'] == 'В ожидании курьера' ? 'selected' : '') . '>В ожидании курьера</option>';
                    echo '</select>';
                    echo '<input type="hidden" name="order_id" value="' . $row['id'] . '">';
                    echo '<input type="submit" name="submit" value="Сохранить">';
                    echo '</form>';
                    echo '</td>';
                }
                else if ($row['status'] === 'Утверждение курьера') {
                    echo '<td>';
                    echo '<form method="post" action="../php/update_status.php">';
                    echo '<input type="hidden" name="order_id" value="'.$row['id'].'">';
                    echo '<input type="hidden" name="status" value="В доставке">';
                    echo '<input type="submit" name="submit" value="Отдать заказ">';
                    echo '</form>';

                    echo '<form method="post" action="../php/update_status.php">';
                    echo '<input type="hidden" name="order_id" value="'.$row['id'].'">';
                    echo '<input type="hidden" name="status" value="В ожидании курьера">';
                    echo '<input type="submit" name="submit" value="Отклонить заказ">';
                    echo '</form>';
                    echo '</td>';
                }
                else {
                    echo '<td>'.$row['status'].'</td>';
                }
                echo '<td>'.$row['comment'].'</td>';
                echo '<td>'.$row['delivery_time'].'</td>';
                echo '<td>'.$row['deliveryman_first_name'].'</td>';
                echo '<td>'.$row['deliveryman_phone_number'].'</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        } else {
            echo '<p>Заказов не найдено</p>';
        }
        $conn->close();
    }

    catch (Exception $e) {
        header("Location: orders.php");
    }

    ?>
<?php endif?>

</body>
</html>