<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../images/favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="../images/favicon.ico" type="image/x-icon">
    <title>Корзина</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #634E42;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            height: 100vh;
        }
        input {
            max-width: 150px;
            background-color: #634E42;
            border-style: solid;
            border-color: transparent;
            color: #F39200;
        }
        nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            width: auto;
            margin-top: 30px;
            background-color: #634E42;
            z-index: 1000;
            text-align: center;

            display: flex;
            justify-content: center;
        }
        .last_button a {
            color: #634E42;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 5px;
            background-color: #F39200;
            transition: background-color 0.3s ease, color 0.3s ease;
            display: flex;
            justify-content: center;
        }

        .last_button a:hover {
            background-color: #634E42;
            color: #F39200;
        }
        nav a:hover {
            background-color: #634E42;
            color: #F39200;
        }
        nav a {
            color: #634E42;
            text-decoration: none;

            padding: 5px 10px;
            border-radius: 5px;
            background-color: #F39200;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        a {
            margin-right: 18px;
        }


        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
            margin-top: 70px;
        }

        th, td {
            border: 1px solid black;
            text-align: left;
            padding: 8px;
            background-color: #F39200;
        }

        th {
            background-color: #F39200;
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
<?php
session_start();

if ($_SESSION['role'] == ""){
    header("Location: auth.php");
    die;
}
else if ($_SESSION['role'] != "user") {
    header("Location: profile.php");
    die;
}
echo '<nav>';
echo "<div><a href='catalog.php'>Каталог</a></div>";
echo "<div><a href='profile.php'>Профиль</a></div>";
echo "<div><a href='orders.php'>Заказы</a></div>";
echo '</nav>';


$connectMySQL = new mysqli('localhost', 'root', 'root', 'Stolovka');




$user_id = $_SESSION['id'];
$products_result = $connectMySQL->query("SELECT product_id FROM `carts_to_products` WHERE `user_id` = '$user_id'");
$products_count = $connectMySQL->query("SELECT COUNT(product_id) as count FROM `carts_to_products` WHERE `user_id` = '$user_id'");;
$row = $products_count->fetch_assoc();
$product_count = $row['count'];
if ($product_count > 0) {
    $_SESSION['isCart'] = "1";
    echo '<div class="table_orders">';
    echo '<table>';
    echo '<thead>';
    echo '<tr>';
    echo '<th>'."Блюдо".'</th>';
    echo '<th>'."Цена".'</th>';
    echo '<th>'."Ингредиенты".'</th>';
    echo '<th>'."".'</th>';
    echo '<tr>';
    echo '</thead>';
    echo '<tbody>';
    while ($row = $products_result->fetch_assoc())
    {
        $product_id = $row['product_id'];
        $product_result = $connectMySQL->query("SELECT name, price FROM `products` WHERE `id` = '$product_id'")->fetch_assoc();

        echo '<tr id="' . $product_id . '">';
        echo '<td>'.$product_result['name'].'</td>';
        echo '<td>'.$product_result['price'].'</td>';
        echo '<td>';
        $user_ingredients_result = $connectMySQL->query("SELECT ingredient_id FROM `carts_to_products_to_ingredients` WHERE `user_id` = '$user_id' AND `product_id` = '$product_id'");
        while ($row2 = $user_ingredients_result->fetch_assoc())
        {
            $ingredient_id = $row2['ingredient_id'];
            $ingredient_name= $connectMySQL->query("SELECT name FROM `ingredients` WHERE `id` = '$ingredient_id'")->fetch_assoc()['name'];
            if ($connectMySQL->query("SELECT is_included FROM `products_to_ingredients` WHERE `product_id` = '$product_id' AND `ingredient_id` = '$ingredient_id'")->fetch_assoc()['is_included'] == 1)
            {
                echo 'Убрать: ' . $ingredient_name . ' ';
            }
            else
            {
                echo 'Добавить: ' . $ingredient_name . ' ';
            }
        }
        echo '</td>';
        echo '<td>';
        echo '<form action="../php/delete_product_from_cart.php" method="post" onsubmit="return delete_product(this);">';
        echo '<input type="hidden" id="product_id" name="product_id" value="' . $product_id . '">';
        echo '<button type="submit">Удалить</button>';
        echo '</form>';
        echo '</td>';
        echo '</tr>';

    }
    echo '</tbody>';
    echo '</table>';
    echo '<div class="last_button">';
    echo '<a href="making_an_order.php">Оформить заказ</a>';
    echo '</div>';
}
else {
    $_SESSION['isCart'] = "0";
    echo '<h2 style="margin-top: 70px">'."Корзина пустая".'</h2>';
}

?>

<script>
    function delete_product(form) {
        var productId = form.elements["product_id"].value;
        var formData = new FormData();
        formData.append('product_id', productId);
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '../php/delete_product_from_cart.php', true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                location.reload();
            }
        };
        xhr.send(formData);
        return false;
    }
</script>


<script>
    var tdElements = document.querySelectorAll('.table_orders tbody td');
    if (tdElements.length === 0) {
        var orderLink = document.querySelector('a[href="making_an_order.php"]');
        orderLink.style.pointerEvents = 'none';
        orderLink.style.color = 'grey';
    }
</script>
</body>