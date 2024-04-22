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
        input[type="password"] {
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
        <input type="text" name="first_name" placeholder="Введите имя" required>
        <input type="text" name="phone_number" placeholder="Введите номер телефона" required>
        <input type="password" name="password" placeholder="Введите пароль" required>
        <input type="text" name="address" placeholder="Введите адрес" required>
        <input type="submit" name="submit" value="Отправить">
    </form>
    <a href="auth.php">Уже есть аккаунт</a>
</div>
</body>
</html>
