<!DOCTYPE html>
<html lang="ru">
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
    <meta charset="UTF-8">
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
        }
        nav a {
            color: #634E42;
            text-decoration: none;
            margin-right: 15px;
            padding: 5px 10px;
            border-radius: 5px;
            background-color: #F39200;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        nav a:hover {
            background-color: #634E42;
            color: #F39200;
        }
        .container {
            background-color: #F39200;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            overflow: auto;
            max-height: 80vh;
            justify-content: center;
            align-items: center;
        }
        .container::-webkit-scrollbar {
            width: 0;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        .image-wrapper {
            position: relative;
            overflow: hidden;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;

        }
        .image-wrapper:hover .overlay {
            opacity: 1;
        }
        .product {
            display: block;
            width: 400px;
            max-height: 300px;
        }
        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .overlay-content {
            text-align: center;
            padding: 10px;
        }
        .overlay-content button {
            background-color: #634E42;
            color: #F39200;
            border: none;
            border-radius: 5px;
            padding: 8px 20px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .overlay-content button:hover {
            background-color: #F39200;
            color: #AA6304;
        }
        footer {
            text-align: center;
            margin-top: 20px;
        }
        footer a {
            text-decoration: none;
            color: #007bff;
            margin: 0 10px;
        }
        footer a:hover {
            text-decoration: underline;
        }
        .img1 {
            max-width: 300px;
        }
    </style>
    <script>
        function addToCart(form)
        {
            var productId = form.elements["product_id"].value;
            var checkedIngredients = [];
            var checkboxes = form.querySelectorAll('input[type="checkbox"]:checked');
            checkboxes.forEach(function(checkbox) {
                checkedIngredients.push(checkbox.value);
            });

            var formData = new FormData();
            formData.append('product_id', productId);
            formData.append('checked_ingredients', JSON.stringify(checkedIngredients));

            var xhr = new XMLHttpRequest();

            xhr.open('POST', '../php/add_product.php', true);
            xhr.send(formData);



            setTimeout(countChanger, 500);

            return false;
        }

        function countChanger()
        {
            var formData = new FormData();
            var xhr = new XMLHttpRequest();

            xhr.onload = function()
            {
                if (xhr.status === 200)
                {
                    console.log(document.getElementById('count').innerText)
                    document.getElementById('count').innerText = "Корзина " + xhr.responseText;
                }
                else
                {
                    console.error('Ошибка: ' + xhr.statusText);
                }
            };

            xhr.open('POST', '../php/change_number.php', true);
            xhr.send(formData);

            return false;
        }
    </script>
</head>
<body>
<nav>
    <a href='orders.php'>Заказы</a>
    <?php
    session_start();
    $connectMySQL = new mysqli('localhost', 'root', 'root', 'Stolovka');

    $user_id = $_SESSION['id'];
    $result1 = $connectMySQL->query("SELECT * FROM carts_to_products WHERE user_id = '$user_id'");
    $count = $result1->num_rows;

    if ($_SESSION['role'] == "user") {
        echo "<a href='profile.php'>Профиль</a>";
        echo "<a href='cart.php' id='count'>Корзина " . $count . "</a>";
    } elseif ($_SESSION['role'] == "manager") {
        echo "<a href='profile.php'>Профиль</a>";
    }
    ?>
</nav>
<div class="container">
    <img class="img1" src="../images/STOLOVKA.png">
    <?php
    $connectMySQL = new mysqli('localhost', 'root', 'root', 'Stolovka');
    $result = $connectMySQL->query("SELECT * FROM products");
    while ($row = $result->fetch_assoc()) {
        ?>
        <div class="image-wrapper">
            <img class="product" src="..<?php echo $row['image_src']; ?>" alt="Тут должно быть фото" data-fancybox="gallery">
            <div class="overlay">
                <div class="overlay-content">
                    <br>
                    <?php echo $row['name']; ?>
                    <br>
                    <?php echo $row['price'] . " руб."; ?>
                    <br>
                    <form action="../php/add_product.php" method="post" onsubmit="return addToCart(this);">
                        <?php
                        $temp = $row['id'];
                        $result2 = $connectMySQL->query("SELECT ingredient_id, is_included FROM products_to_ingredients WHERE product_id = '$temp'");
                        while ($row2 = $result2->fetch_assoc()) {
                            $ingredient_id = $row2['ingredient_id'];
                            $is_included = $row2['is_included'];
                            $result3 = $connectMySQL->query("SELECT name FROM ingredients WHERE id = '$ingredient_id'");
                            echo $result3->fetch_assoc()['name'];
                            ?>
                            <input type="checkbox" name="ingredients[]" value="<?php echo $ingredient_id; ?>" <?php echo ($is_included == 1 ? 'checked' : ''); ?>>
                            <br>
                        <?php } ?>
                        <input type="hidden" name="product_id" value="<?php echo $row['id'] ?>">
                        <button type="submit">Добавить в корзину</button>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }
    ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.4.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
</body>
</html>
