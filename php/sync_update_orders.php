<?php
session_start();

if ($_SESSION['role'] == 'user') {
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
            while ($row = $result->fetch_assoc()) {
                ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['address'] ?></td>
                    <td><?= $row['status'] ?></td>
                    <td><?= $row['delivery_time'] ?></td>
                    <td>
                        <?php
                        $order_id = $row['id'];
                        $dishes = explode("<br>", $row['dishes']);
                        for ($i = 0; $i < count($dishes); $i++) {
                            echo $dishes[$i];
                            $dish_name = $dishes[$i];
                            $dish_id = $conn->query("SELECT id FROM `products` WHERE `name` = '$dish_name'")->fetch_assoc()['id'];
                            $user_ingredients_result = $conn->query("SELECT ingredient_id FROM `orders_to_products_to_ingredients` WHERE `order_id` = '$order_id' AND `product_id` = '$dish_id'");
                            if ($user_ingredients_result->num_rows > 0) {
                                echo '<br>';
                            }
                            while ($row2 = $user_ingredients_result->fetch_assoc()) {
                                $ingredient_id = $row2['ingredient_id'];
                                $ingredient_name= $conn->query("SELECT name FROM `ingredients` WHERE `id` = '$ingredient_id'")->fetch_assoc()['name'];
                                if ($conn->query("SELECT is_included FROM `products_to_ingredients` WHERE `product_id` = '$dish_id' AND `ingredient_id` = '$ingredient_id'")->fetch_assoc()['is_included'] == 1) {
                                    echo 'Убрать: ' . $ingredient_name;
                                } else {
                                    echo 'Добавить: ' . $ingredient_name;
                                }
                                if ($user_ingredients_result->num_rows > 1)
                                {
                                    echo '<br>';
                                }
                            }
                            if ($i < count($dishes) - 1) {
                                echo '<br>';
                            }
                            echo '<br>';
                        }
                        ?>
                    </td>
                    <td><?= $row['comment'] ?></td>
                    <td><?= $row['total_price'] ?> RUB</td>
                    <td><?= $row['deliveryman_first_name'] ?></td>
                    <td><?= $row['deliveryman_phone_number'] ?></td>
                </tr>
            <?php }
        } else {
            echo '<h2 style="margin-top: 70px">Заказов не найдено</h2>';
        }
        $conn->close();
    } catch (Exception $e) {
        echo 'Ошибка: ',  $e->getMessage(), "\n";
    }
} elseif ($_SESSION['role'] == 'manager') {
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
        ?>
                <?php while($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td <?php if ($row['status'] === 'Проблема с покупателем!') echo 'style="background-color: #d36e70;"'; ?>
                            <?php if ($row['status'] === 'Доставлен') echo 'style="background-color: #70d375;"'; ?>><?= $row['id'] ?></td>
                        <td <?php if ($row['status'] === 'Проблема с покупателем!') echo 'style="background-color: #d36e70;"'; ?>
                            <?php if ($row['status'] === 'Доставлен') echo 'style="background-color: #70d375;"'; ?>><?= $row['first_name'] ?></td>
                        <td <?php if ($row['status'] === 'Проблема с покупателем!') echo 'style="background-color: #d36e70;"'; ?>
                            <?php if ($row['status'] === 'Доставлен') echo 'style="background-color: #70d375;"'; ?>><?= $row['phone_number'] ?></td>
                        <td <?php if ($row['status'] === 'Проблема с покупателем!') echo 'style="background-color: #d36e70;"'; ?>
                            <?php if ($row['status'] === 'Доставлен') echo 'style="background-color: #70d375;"'; ?>><?= $row['address'] ?></td>
                        <?php if ($row['status'] === 'Доставлен' || $row['status'] === 'Заказ отменен') { ?>
                            <td <?php if ($row['status'] === 'Проблема с покупателем!') echo 'style="background-color: #d36e70;"'; ?>
                                <?php if ($row['status'] === 'Доставлен') echo 'style="background-color: #70d375;"'; ?>><?= $row['status'] ?></td>
                        <?php } else { ?>
                            <td <?php if ($row['status'] === 'Проблема с покупателем!') echo 'style="background-color: #d36e70;"'; ?>
                                <?php if ($row['status'] === 'Доставлен') echo 'style="background-color: #70d375;"'; ?>>
                                <form method="post" action="../php/update_status.php">
                                    <select name="status">
                                        <option value="Ожидает рассмотрения" <?= ($row['status'] == 'Ожидает рассмотрения' ? 'selected' : '') ?>>Ожидает рассмотрения</option>
                                        <option value="Принят" <?= ($row['status'] == 'Принят' ? 'selected' : '') ?>>Принят</option>
                                        <option value="На кухне" <?= ($row['status'] == 'На кухне' ? 'selected' : '') ?>>На кухне</option>
                                        <option value="В ожидании курьера" <?= ($row['status'] == 'В ожидании курьера' ? 'selected' : '') ?>>В ожидании курьера</option>
                                        <option value="Утверждение курьера" <?= ($row['status'] == 'Утверждение курьера' ? 'selected' : '') ?>>Утверждение курьера</option>
                                        <option value="В доставке" <?= ($row['status'] == 'В доставке' ? 'selected' : '') ?>>В доставке</option>
                                        <option value="Проблема с покупателем!" <?= ($row['status'] == 'Проблема с покупателем!' ? 'selected' : '') ?>>Проблема с покупателем!</option>
                                        <option value="Заказ отменен" <?= ($row['status'] == 'Заказ отменен' ? 'selected' : '') ?>>Заказ отменен</option>
                                    </select>
                                    <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                                    <input type="submit" name="submit" value="Сохранить">
                                </form>
                            </td>
                        <?php } ?>
                        <td <?php if ($row['status'] === 'Проблема с покупателем!') echo 'style="background-color: #d36e70;"'; ?>
                            <?php if ($row['status'] === 'Доставлен') echo 'style="background-color: #70d375;"'; ?>><?= $row['delivery_time'] ?></td>
                        <td <?php if ($row['status'] === 'Проблема с покупателем!') echo 'style="background-color: #d36e70;"'; ?>
                            <?php if ($row['status'] === 'Доставлен') echo 'style="background-color: #70d375;"'; ?>><?= $row['order_time'] ?></td>
                        <td <?php if ($row['status'] === 'Проблема с покупателем!') echo 'style="background-color: #d36e70;"'; ?>
                            <?php if ($row['status'] === 'Доставлен') echo 'style="background-color: #70d375;"'; ?>>
                            <?php
                            $order_id = $row['id'];
                            $dishes = explode("<br>", $row['dishes']);
                            for ($i = 0; $i < count($dishes); $i++) {
                                echo $dishes[$i];
                                $dish_name = $dishes[$i];
                                $dish_id = $conn->query("SELECT id FROM `products` WHERE `name` = '$dish_name'")->fetch_assoc()['id'];
                                $user_ingredients_result = $conn->query("SELECT ingredient_id FROM `orders_to_products_to_ingredients` WHERE `order_id` = '$order_id' AND `product_id` = '$dish_id'");
                                if ($user_ingredients_result->num_rows > 0) {
                                    echo '<br>';
                                }
                                while ($row2 = $user_ingredients_result->fetch_assoc()) {
                                    $ingredient_id = $row2['ingredient_id'];
                                    $ingredient_name= $conn->query("SELECT name FROM `ingredients` WHERE `id` = '$ingredient_id'")->fetch_assoc()['name'];
                                    if ($conn->query("SELECT is_included FROM `products_to_ingredients` WHERE `product_id` = '$dish_id' AND `ingredient_id` = '$ingredient_id'")->fetch_assoc()['is_included'] == 1) {
                                        echo 'Убрать: ' . $ingredient_name;
                                    } else {
                                        echo 'Добавить: ' . $ingredient_name;
                                    }
                                    if ($user_ingredients_result->num_rows > 1)
                                    {
                                        echo '<br>';
                                    }
                                }
                                if ($i < count($dishes) - 1) {
                                    echo '<br>';
                                }
                                echo '<br>';
                            }
                            ?>
                        </td>
                        <td <?php if ($row['status'] === 'Проблема с покупателем!') echo 'style="background-color: #d36e70;"'; ?>
                            <?php if ($row['status'] === 'Доставлен') echo 'style="background-color: #70d375;"'; ?>><?= $row['comment'] ?></td>
                        <td <?php if ($row['status'] === 'Проблема с покупателем!') echo 'style="background-color: #d36e70;"'; ?>
                            <?php if ($row['status'] === 'Доставлен') echo 'style="background-color: #70d375;"'; ?>><?= $row['total_price'] ?> RUB</td>
                        <td <?php if ($row['status'] === 'Проблема с покупателем!') echo 'style="background-color: #d36e70;"'; ?>
                            <?php if ($row['status'] === 'Доставлен') echo 'style="background-color: #70d375;"'; ?>><?= $row['deliveryman_first_name'] ?></td>
                        <td <?php if ($row['status'] === 'Проблема с покупателем!') echo 'style="background-color: #d36e70;"'; ?>
                            <?php if ($row['status'] === 'Доставлен') echo 'style="background-color: #70d375;"'; ?>><?= $row['deliveryman_phone_number'] ?></td>
                    </tr>
                <?php } ?>
    <?php } else {
        echo '<h2 style="margin-top: 70px">Заказов не найдено</h2>';
    }
    $conn->close();
}
catch (Exception $e) {
    header("Location: orders.php");
}
}
elseif ($_SESSION['role'] == 'deliveryman') {
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

        if ($result->num_rows > 0):
            ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td <?php if ($row['status'] === 'Доставлен') echo 'style="background-color: #70d375;"'; ?>><?php echo $row['id']; ?></td>
                            <td <?php if ($row['status'] === 'Доставлен') echo 'style="background-color: #70d375;"'; ?>><?php echo $row['first_name']; ?></td>
                            <td <?php if ($row['status'] === 'Доставлен') echo 'style="background-color: #70d375;"'; ?>><?php echo $row['phone_number']; ?></td>
                            <td <?php if ($row['status'] === 'Доставлен') echo 'style="background-color: #70d375;"'; ?>><?php echo $row['address']; ?></td>
                            <?php if ($row['status'] === 'В ожидании курьера'): ?>
                                <td <?php if ($row['status'] === 'Доставлен') echo 'style="background-color: #70d375;"'; ?>>
                                    <form method="post" action="../php/take_order.php">
                                        <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                        <input type="submit" name="take_order" value="Взять заказ">
                                    </form>
                                </td>
                            <?php elseif ($row['status'] === 'В доставке'): ?>
                                <td <?php if ($row['status'] === 'Доставлен') echo 'style="background-color: #70d375;"'; ?>>
                                    <form method="post" action="../php/update_status.php">
                                        <select name="status">
                                            <option value="В доставке" <?php echo ($row['status'] == 'В доставке' ? 'selected' : ''); ?>>В доставке</option>
                                            <option value="Доставлен" <?php echo ($row['status'] == 'Доставлен' ? 'selected' : ''); ?>>Доставлен</option>
                                        </select>
                                        <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                        <input type="submit" name="submit" value="Сохранить">
                                    </form>

                                    <form method="post" action="../php/update_status.php">
                                        <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                        <input type="hidden" name="status" value="Проблема с покупателем!">
                                        <input type="submit" name="submit" value="Проблема с покупателем!">
                                    </form>
                                </td>
                            <?php elseif ($row['status'] === 'Проблема с покупателем!'): ?>
                                <td <?php if ($row['status'] === 'Доставлен') echo 'style="background-color: #70d375;"'; ?>>
                                    <form method="post" action="../php/update_status.php">
                                        <select name="status">
                                            <option value="В доставке" <?php echo ($row['status'] == 'В доставке' ? 'selected' : ''); ?>>В доставке</option>
                                            <option value="Доставлен" <?php echo ($row['status'] == 'Доставлен' ? 'selected' : ''); ?>>Доставлен</option>
                                            <option value="Проблема с покупателем!" <?php echo ($row['status'] == 'Проблема с покупателем!' ? 'selected' : ''); ?>>Проблема с покупателем!</option>
                                        </select>
                                        <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                        <input type="submit" name="submit" value="Сохранить">
                                    </form>
                                </td>
                            <?php else: ?>
                                <td <?php if ($row['status'] === 'Доставлен') echo 'style="background-color: #70d375;"'; ?>><?php echo $row['status']; ?></td>
                            <?php endif; ?>
                            <td <?php if ($row['status'] === 'Доставлен') echo 'style="background-color: #70d375;"'; ?>><?php echo $row['delivery_time']; ?></td>
                            <td <?php if ($row['status'] === 'Доставлен') echo 'style="background-color: #70d375;"'; ?>><?php echo $row['order_time']; ?></td>
                            <td <?php if ($row['status'] === 'Доставлен') echo 'style="background-color: #70d375;"'; ?>>
                                <?php
                                $order_id = $row['id'];
                                $dishes = explode("<br>", $row['dishes']);
                                foreach ($dishes as $dish_name) {
                                    echo $dish_name;
                                    $dish_id = $conn->query("SELECT id FROM `products` WHERE `name` = '$dish_name'")->fetch_assoc()['id'];
                                    $user_ingredients_result = $conn->query("SELECT ingredient_id FROM `orders_to_products_to_ingredients` WHERE `order_id` = '$order_id' AND `product_id` = '$dish_id'");
                                    if ($user_ingredients_result->num_rows > 0) {
                                        echo '<br>';
                                    }
                                    while ($row2 = $user_ingredients_result->fetch_assoc()) {
                                        $ingredient_id = $row2['ingredient_id'];
                                        $ingredient_name = $conn->query("SELECT name FROM `ingredients` WHERE `id` = '$ingredient_id'")->fetch_assoc()['name'];
                                        if ($conn->query("SELECT is_included FROM `products_to_ingredients` WHERE `product_id` = '$dish_id' AND `ingredient_id` = '$ingredient_id'")->fetch_assoc()['is_included'] == 1) {
                                            echo 'Убрать: ' . $ingredient_name;
                                        } else {
                                            echo 'Добавить: ' . $ingredient_name;
                                        }
                                        if ($user_ingredients_result->num_rows > 1)
                                        {
                                            echo '<br>';
                                        }
                                    }
                                    if ($i < count($dishes) - 1) {
                                        echo '<br>';
                                    }
                                    echo '<br>';
                                }
                                ?>
                            </td>
                            <td <?php if ($row['status'] === 'Доставлен') echo 'style="background-color: #70d375;"'; ?>><?php echo $row['comment']; ?></td>
                            <td <?php if ($row['status'] === 'Доставлен') echo 'style="background-color: #70d375;"'; ?>><?php echo $row['total_price']." RUB"; ?></td>
                            <td <?php if ($row['status'] === 'Доставлен') echo 'style="background-color: #70d375;"'; ?>><?php echo $row['deliveryman_first_name']; ?></td>
                            <td <?php if ($row['status'] === 'Доставлен') echo 'style="background-color: #70d375;"'; ?>><?php echo $row['deliveryman_phone_number']; ?></td>
                        </tr>
                    <?php endwhile; ?>
        <?php
        else:
            echo '<h2 style="margin-top: 70px">Заказов не найдено</h2>';
        endif;
        $conn->close();
    }
    catch (Exception $e) {
        header("Location: orders.php");
    }
}

elseif ($_SESSION['role'] == 'cook') {
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

        if ($result->num_rows > 0):
            ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['order_time']; ?></td>
                            <td>
                                <?php
                                $order_id = $row['id'];
                                $dishes = explode("<br>", $row['dishes']);
                                foreach ($dishes as $dish_name) {
                                    echo $dish_name;
                                    $dish_id = $conn->query("SELECT id FROM `products` WHERE `name` = '$dish_name'")->fetch_assoc()['id'];
                                    $user_ingredients_result = $conn->query("SELECT ingredient_id FROM `orders_to_products_to_ingredients` WHERE `order_id` = '$order_id' AND `product_id` = '$dish_id'");
                                    if ($user_ingredients_result->num_rows > 0) {
                                        echo '<br>';
                                    }
                                    while ($row2 = $user_ingredients_result->fetch_assoc()) {
                                        $ingredient_id = $row2['ingredient_id'];
                                        $ingredient_name = $conn->query("SELECT name FROM `ingredients` WHERE `id` = '$ingredient_id'")->fetch_assoc()['name'];
                                        if ($conn->query("SELECT is_included FROM `products_to_ingredients` WHERE `product_id` = '$dish_id' AND `ingredient_id` = '$ingredient_id'")->fetch_assoc()['is_included'] == 1) {
                                            echo 'Убрать: ' . $ingredient_name;
                                        } else {
                                            echo 'Добавить: ' . $ingredient_name;
                                        }
                                        if ($user_ingredients_result->num_rows > 1)
                                        {
                                            echo '<br>';
                                        }
                                    }
                                    if ($i < count($dishes) - 1) {
                                        echo '<br>';
                                    }
                                    echo '<br>';
                                }
                                ?>
                            </td>
                            <?php if ($row['status'] !== 'Утверждение курьера' && $row['status'] !== 'В доставке'): ?>
                                <td>
                                    <form method="post" action="../php/update_status.php">
                                        <select name="status">
                                            <option value="Принят" <?php echo ($row['status'] == 'Принят' ? 'selected' : ''); ?>>Принят</option>
                                            <option value="На кухне" <?php echo ($row['status'] == 'На кухне' ? 'selected' : ''); ?>>На кухне</option>
                                            <option value="В ожидании курьера" <?php echo ($row['status'] == 'В ожидании курьера' ? 'selected' : ''); ?>>В ожидании курьера</option>
                                        </select>
                                        <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                        <input type="submit" name="submit" value="Сохранить">
                                    </form>
                                </td>
                            <?php elseif ($row['status'] === 'Утверждение курьера'): ?>
                                <td>
                                    <form method="post" action="../php/update_status.php">
                                        <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                        <input type="hidden" name="status" value="В доставке">
                                        <input type="submit" name="submit" value="Отдать заказ">
                                    </form>

                                    <form method="post" action="../php/update_status.php">
                                        <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                        <input type="hidden" name="status" value="В ожидании курьера">
                                        <input type="submit" name="submit" value="Отклонить заказ">
                                    </form>
                                </td>
                            <?php else: ?>
                                <td><?php echo $row['status']; ?></td>
                            <?php endif; ?>
                            <td><?php echo $row['comment']; ?></td>
                            <td><?php echo $row['delivery_time']; ?></td>
                            <td><?php echo $row['deliveryman_first_name']; ?></td>
                            <td><?php echo $row['deliveryman_phone_number']; ?></td>
                        </tr>
                    <?php endwhile; ?>
        <?php
        else:
            echo '<h2 style="margin-top: 70px">Заказов не найдено</h2>';
        endif;
        $conn->close();
    }
    catch (Exception $e) {
        header("Location: orders.php");
    }
}

?>
