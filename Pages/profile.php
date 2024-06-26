<?php
session_start();
$_SESSION['isCart'] = "0";
$connectMySQL = new mysqli('localhost', 'root', 'root', 'stolovka');

if (!isset($_SESSION['id'])) {
    header("Location: auth.php");
    die();
}
elseif ($_SESSION['role'] == "manager"){
    header("Location: catalog.php");
    die;
}
elseif ($_SESSION['role'] == "cook")
{
    header("Location: orders.php");
    die;
}

$id = $_SESSION['id'];
$role = $_SESSION['role'];

$username = $connectMySQL->query("SELECT `first_name` FROM `users` WHERE `id` = '$id'")->fetch_assoc()['first_name'];
$phone_number = $connectMySQL->query("SELECT `phone_number` FROM `users` WHERE `id` = '$id'")->fetch_assoc()['phone_number'];
$address = $connectMySQL->query("SELECT `address` FROM `users` WHERE `id` = '$id'")->fetch_assoc()['address'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../images/favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="../images/favicon.ico" type="image/x-icon">
    <title>Профиль</title>
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
            text-align: center;
            max-width: 300px;
        }
        .container_another {
            text-align: left;
        }
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
        #show_form {
            background-image: url('../images/SHESTERNYA1.png');
            background-color: transparent;
            width: 35px;
            height: 35px;
            background-size: cover;
            border: none;
        }
        #logout_btn {
            background-image: url('../images/VYHOD1.png');
            background-color: transparent;
            width: 35px;
            height: 35px;
            margin-right: 6px;
            background-size: cover;
            border: none;
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
        <img src="../images/STOLOVKA.png">
        <h1><?php echo "Привет, $username!"; ?></h1>
        <div class="container_another">
            <h3>Телефон: <?php echo $phone_number; ?></h3>
            <h3>Адрес: <?php echo $address; ?></h3>
            <?php
            if ($_SESSION['role'] == "user") {
                echo '<h3>'."Покупатель".'</h3>';
            } elseif ($_SESSION['role'] == "manager") {
                echo '<h3>'."Менеджер".'</h3>';
            }
            elseif ($_SESSION['role'] == "deliveryman") {
                echo '<h3>'."Курьер".'</h3>';
            }
            else {
                echo '<h3>'."Кухня".'</h3>';
            }
            ?>
        </div>

        <form id="editForm" style="display: none;" action="../php/userDataChange.php" method="post">
            <input type="text" maxlength="30" name="edit_f_name" id="name" style="margin-bottom: 5px;" value="<?php echo $username; ?>"><br>
            <input type="text" maxlength="100" id="address" name="edit_add" style="margin-bottom: 5px;" value="<?php echo $address; ?>"><br>
            <input type="submit" name="edit_btn" style="margin-bottom: 5px;" value="Внести изменения">
        </form>
        <div style="margin-bottom: 5px; display: flex; justify-content: center;">
            <button id="show_form" onclick="hideForm()"></button>
        </div>

        <div style="margin-bottom: 5px; display: flex; justify-content: center;">
            <form action="../php/logout.php" method="post">
                <button type="submit"
                        id="logout_btn"></button>
            </form>
        </div>
    </div>
    <script>
        function hideForm() {
            const form = document.getElementById('editForm');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</footer>
<script>
    let addressInput = document.getElementById('address');
    addressInput.addEventListener('input', function(event) {
        let inputValue = event.target.value;
        for (let i = 0; i < inputValue.length; i++) {
            if (!(/[а-яА-Я0-9\s,.-]/.test(inputValue[i]))) {
                inputValue = inputValue.slice(0, i) + inputValue.slice(i + 1);
                addressInput.value = inputValue;
                break;
            }
        }
    });
    let nameInput = document.getElementById('name');
    nameInput.addEventListener('input', function(event) {
        let inputValue = event.target.value;
        for (let i = 0; i < inputValue.length; i++) {
            if (!(/[а-яА-ЯёЁ]/.test(inputValue[i]))) {
                inputValue = inputValue.slice(0, i) + inputValue.slice(i + 1);
                nameInput.value = inputValue;
                break;
            }
        }
    });
</script>
</body>
</html>