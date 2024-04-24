<?php
session_start();
$_SESSION['isCart'] = "0";
if ($_SESSION['role'] == ""){
    header("Location: auth.php");
    die;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
    <meta charset="UTF-8">
    <title>Каталог</title>
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
            margin-top: 30px;
        }
        .container::-webkit-scrollbar {
            width: 0;
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
            display: flex;
            flex-direction: column;
            align-items: center;
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
        select {
            background-color: #634E42;
            border-color: transparent;
            border-style: solid;
            color: #F39200;
        }
    </style>
    <script>
        function removeIngredient(productId, ingredientId) {
            var formData = new FormData();
            formData.append('product_id', productId);
            formData.append('ingredient_id', ingredientId);

            var xhr = new XMLHttpRequest();
            xhr.open('POST', '../php/delete_ingredients.php', true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    location.reload();
                }
            };
            xhr.send(formData);
        }
        function addToCart(form) {
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

            xhr.open('POST', '../php/add_product_to_cart.php', true);
            xhr.send(formData);

            setTimeout(countChanger, 500);

            return false;
        }

        function countChanger() {
            var formData = new FormData();
            var xhr = new XMLHttpRequest();

            xhr.onload = function() {
                if (xhr.status === 200) {
                    console.log(document.getElementById('count').innerText)
                    document.getElementById('count').innerText = "Корзина (" + xhr.responseText + ")";
                } else {
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
    $connectMySQL = new mysqli('localhost', 'root', 'root', 'stolovka');

    $user_id = $_SESSION['id'];
    $result1 = $connectMySQL->query("SELECT * FROM carts_to_products WHERE user_id = '$user_id'");
    $count = $result1->num_rows;

    if ($_SESSION['role'] == "user") {
        echo "<a href='profile.php'>Профиль</a>";
        echo "<a href='cart.php' id='count'>Корзина " . "(" . $count . ")" . "</a>";
    } elseif ($_SESSION['role'] == "manager") {
        echo "<a href='add_new_product.php'>Добавление продукта</a>";
        echo "<a href='../php/logout.php'>Выход</a>";
    }
    ?>
</nav>
<div class="container">
    <img class="img1" src="../images/STOLOVKA.png">
    <h2>Меню</h2>
    <?php
    $connectMySQL = new mysqli('localhost', 'root', 'root', 'stolovka');
    $result = $connectMySQL->query("SELECT * FROM products");

    if ($_SESSION['role'] == "user") {
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
                        <form action="../php/add_product_to_cart.php" method="post" onsubmit="return addToCart(this);">
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
    } elseif ($_SESSION['role'] == 'manager') {
        while ($row = $result->fetch_assoc()) {
            ?>
            <div class="image-wrapper">
                <img class="product" src="..<?php echo $row['image_src']; ?>" alt="Тут должно быть фото" data-fancybox="gallery">
                <div class="overlay">
                    <div class="overlay-content">
                        <form action="../php/update_product_name.php" method="post">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            Название: <input id="name_products" type="text" maxlength="50" name="name" value="<?php echo $row['name']; ?>">
                            <button type="submit">Ок</button>
                        </form>
                        <form action="../php/update_price.php" method="post">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            Цена: <input type="text" id="price" maxlength="11" name="price" value="<?php echo $row['price']; ?>">
                            <button type="submit">Ок</button>
                        </form>
                        <form action="../php/update_image_src.php" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            Путь к картинке: <input type="file" name="image">
                            <button type="submit">Ок</button>
                        </form>
                        <form action="../php/add_ingredients.php" method="post">
                            <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                            Новый ингредиент:
                            <input maxlength="30" id="ingredient" type="text" name="new_ingredient_name">
                            <button type="submit">Ок</button>
                        </form>
                        <form action="../php/delete_ingredients.php" method="post">
                            <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                            Удалить ингредиент:
                            <select name="ingredient_id">
                                <?php
                                $ingredientsResult = $connectMySQL->query("SELECT * FROM ingredients INNER JOIN products_to_ingredients ON ingredients.id = products_to_ingredients.ingredient_id WHERE product_id = '{$row['id']}'");
                                while ($ingredient = $ingredientsResult->fetch_assoc()) {
                                    echo "<option value='" . $ingredient['ingredient_id'] . "'>" . $ingredient['name'] . "</option>";
                                }
                                ?>
                            </select>
                            <button type="submit">Ок</button>
                        </form>
                        <form action="../php/delete_product.php" method="post">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <button type="submit">Удалить продукт</button>
                        </form>
                    </div>
                </div>
            </div>
            <?php
        }
    }
    else {
        header("Location: profile.php");
    }
    ?>
</div>
<script>
    let price = document.getElementById('price');
    price.addEventListener('keydown', (e) => {
        if(['0','1','2','3','4', '5', '6', '7', '8', '9', 'Backspace', 'ControlLeft', 'Delete'].indexOf(e.key) !== -1){

        } else {
            e.preventDefault();
        }
    });

    let nameProductsInput = document.getElementById('name_products');
    nameProductsInput.addEventListener('input', function(event) {
        let inputValue = event.target.value;
        for (let i = 0; i < inputValue.length; i++) {
            if (!(/[а-яА-ЯёЁ]/.test(inputValue[i]))) {
                inputValue = inputValue.slice(0, i) + inputValue.slice(i + 1);
                nameProductsInput.value = inputValue;
                break;
            }
        }
    });

    let ingredientInput = document.getElementById('ingredient');
    ingredientInput.addEventListener('input', function(event) {
        let inputValue = event.target.value;
        for (let i = 0; i < inputValue.length; i++) {
            if (!(/[а-яА-ЯёЁ]/.test(inputValue[i]))) {
                inputValue = inputValue.slice(0, i) + inputValue.slice(i + 1);
                ingredientInput.value = inputValue;
                break;
            }
        }
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.4.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
</body>
</html>
