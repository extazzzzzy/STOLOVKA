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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="../images/favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="../images/favicon.ico" type="image/x-icon">
    <title>Авторизация</title>
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
            text-align: center;
        }
        h3 {
            color: #634E42;
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
        form {
            display: flex;
            flex-direction: column;
        }
        input[type="text"],
        input[type="password"] {
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
        .captcha {
            display: flex;
            flex-direction: column;
            align-items: center;
            border-color: #634E42;
        }
        .captcha img {
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .captcha input[type="text"] {
            width: calc(100% - 25px);
            text-align: center;
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
        <h1>Авторизация</h1>
        <form action="../php/authorization.php" method="post">
            <input id="ph_num" maxlength="11" type="text" name="phone_number" placeholder="Введите номер телефона" required>
            <input type="password" maxlength="30" name="password" placeholder="Введите пароль" required>
            <div class="captcha">
                <h3>Проверочный код</h3>
                <img src="../php/captcha.php" alt="CAPTCHA"/>
                <input type="text" id="captcha" maxlength="5" name="captcha" placeholder="Введите код с картинки" required>
                <input type="submit" value="Войти">
            </div>
        </form>
        <a href="register.php">Зарегистрироваться</a>
    </div>
    <script>
        let phone_number = document.getElementById('ph_num');

        phone_number.addEventListener('keydown', (e) => {
        if(['0','1','2','3','4', '5', '6', '7', '8', '9', 'Backspace', 'ControlLeft', 'Delete'].indexOf(e.key) !== -1){
            
        } else {
            e.preventDefault();
        }
        });

        let captchaInput = document.getElementById('captcha');
        captchaInput.addEventListener('input', function(event) {
            let inputValue = event.target.value;
            for (let i = 0; i < inputValue.length; i++) {
                if (!(/[a-zA-Z]/.test(inputValue[i]))) {
                    inputValue = inputValue.slice(0, i) + inputValue.slice(i + 1);
                    captchaInput.value = inputValue;
                    break;
                }
            }
        });
    </script>
</body>
</html>
