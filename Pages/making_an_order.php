<?php
session_start();
$connectMySQL = new mysqli('localhost', 'root', 'root', 'stolovka');

if (!isset($_SESSION['id']) || $_SESSION['role'] != "user") {
    header("Location: auth.php");
    die();
}
else if ($_SESSION['isCart'] == "0") {
    header("Location: catalog.php");
    die();
}
$id = $_SESSION['id'];

$address = $connectMySQL->query("SELECT `address` FROM `users` WHERE `id` = '$id'")->fetch_assoc()['address'];

//$timezone = '+5'; //Время ЕКБ
$timezone = '+5'; //Время ЕКБ
$time_order =  date('Y-m-d H:i:s',time()+($timezone*3600)); //взяли время с компа с учётом час.пояса

$time_order = strtotime($time_order); //перевод времени в строковой формат

function set_deliv_Time($hour, $time_order) {

    $time_start_work = strtotime('080000');
    $time_finish_work = strtotime('220000');

    if ($time_start_work <= strtotime("+$hour hour", $time_order) && strtotime("+$hour hour", $time_order) <= $time_finish_work)
    {
        if ($hour == 0)
            echo "Как можно скорее";
        else
            echo date('H:i:s', strtotime("+$hour hour", $time_order));  //проверка на часы работы ^
    }
    else if ($hour == 0)
        echo "В настоящее время срочная доставка недоступна";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Оформление заказа</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #634E42;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        img {
            max-width: 300px;
        }
        .container {
            background-color: #F39200;
            border-radius: 8px;
            padding: 20px;
            width: 400px;
            text-align: center;
        }
        /*.container_another {
            text-align: left;
            margin-left: 74px;
        }*/
        h1, h3 {
            color: #634E42;
            margin-bottom: 10px;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        input[type="text"] {
            color: #F39200;
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 10px;
            background-color: #634E42;
            border-color: #634E42;
            border-radius: 5px;
            border-style: solid;
            outline: none;
            font-size: 16px;
        }
        ::placeholder {
            color: #F39200;
        }
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #634E42;
            color: #F39200;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #AA6304;
        }
        header {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
        }
        nav {
            background-color: #634E42;
            padding: 10px 20px;
            border-radius: 8px;
            border: 2px solid #634E42;
            margin-bottom: 30px;
        }

        nav a {
            color: #F39200;
            text-decoration: none;
            margin-right: 15px;
            padding: 5px 10px;
            border-radius: 5px;
            background-color: #634E42;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        nav a:hover {
            background-color: #F39200;
            color: #AA6304;
        }
    </style>
</head>
<body>
<footer>
    <div class="container">
        <nav>
                <a href='orders.php'>Заказы</a>
                <?php
                if ($_SESSION['role'] == "user") {
                    echo "<a href='catalog.php'>Каталог</a>";
                    echo "<a href='cart.php'>Корзина</a>";
                } elseif ($_SESSION['role'] == "manager") {
                    echo "<a href='catalog.php'>Каталог</a>";
                }
                ?>
        </nav>
        <div class="container_another">
            <form id="orderForm" action="../php/add_order.php" method="post">
                <h3>Укажите адрес доставки</h3>
                <input type="text" name="order_add" placeholder="Введите адрес доставки" value="<?php echo $address; ?>"><br>
                <h3>Когда вам доставить заказ?</h3>
                <select name="status">
                    <option><?php set_deliv_Time(0, $time_order) ?></option>
                    <option class='option_visib' value="<?php set_deliv_Time(1, $time_order) ?>"><?php set_deliv_Time(1, $time_order) ?></option>
                    <option class='option_visib' value="<?php set_deliv_Time(2, $time_order) ?>"><?php set_deliv_Time(2, $time_order) ?></option>
                    <option class='option_visib' value="<?php set_deliv_Time(3, $time_order) ?>"><?php set_deliv_Time(3, $time_order) ?></option>
                    <option class='option_visib' value="<?php set_deliv_Time(4, $time_order) ?>"><?php set_deliv_Time(4, $time_order) ?></option>
                    <option class='option_visib' value="<?php set_deliv_Time(5, $time_order) ?>"><?php set_deliv_Time(5, $time_order) ?></option>
                    <option class='option_visib' value="<?php set_deliv_Time(6, $time_order) ?>"><?php set_deliv_Time(6, $time_order) ?></option>
                </select><br>
                <h3>Поле для ваших пожеланий</h3>
                <input type="text" name="comment" placeholder="Напишите что-нибудь :-)" value=""><br>
                <input type="submit" name="order_send_btn" value="Оформить заказ">
            </form>
        </div>
    </div>
</footer>
</body>
</html>

