<?php
session_start();
$_SESSION['isCart'] = "0";
?>
<?php if ($_SESSION['id'] != ''): ?>
    <meta http-equiv="refresh" content="0; url=../Pages/profile.php"/>
<?php endif?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
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
        img{
            max-width: 300px;
            text-align: center;
        }
        .container {
            background-color: #F39200;
            border-radius: 8px;
            padding: 20px;
            width: 300px;
        }

        h1 {
            color: #634E42;
            text-align: center;
            margin-bottom: 20px;
        }
        ::placeholder {
            color:    #F39200;
        }
        form {
            display: flex;
            flex-direction: column;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="number"]{
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

        a {
            text-decoration: none;
            color: #AA6304;
            text-align: center;
            display: block;
            margin-top: 10px;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="container">
    <img src="../images/STOLOVKA.png">
    <h1>Регистрация</h1>
    <form action="../php/registration.php" method="post">
        <input type="text" maxlength="30" id="name" name="first_name" placeholder="Введите имя" required>
        <input type="text" id="ph_num" maxlength="11" name="phone_number" placeholder="Введите номер телефона" required>
        <input type="password" maxlength="30" minlength="6" name="password" placeholder="Введите пароль" required>
        <input type="text" maxlength="100" name="address" id="address" placeholder="Введите адрес" required>
        <input type="submit" name="submit" value="Отправить">
    </form>
    <a href="auth.php">Уже есть аккаунт</a>
</div>


<script>
    let phone_number = document.getElementById('ph_num');
    phone_number.addEventListener('keydown', (e) => {
        if(['0','1','2','3','4', '5', '6', '7', '8', '9', 'Backspace', 'ControlLeft', 'Delete'].indexOf(e.key) !== -1){

        } else {
            e.preventDefault();
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
</script>

</body>
</html>
