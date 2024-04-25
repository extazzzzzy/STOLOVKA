<img class="img1" src="../images/STOLOVKA.png">
<h2>Меню</h2>
<?php
session_start();
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
}
elseif ($_SESSION['role'] == 'manager') {
    while ($row = $result->fetch_assoc()) {
        ?>
        <div class="image-wrapper">
            <img class="product" src="..<?php echo $row['image_src']; ?>" alt="Тут должно быть фото" data-fancybox="gallery">
            <div class="overlay">
                <div class="overlay-content">
                    <form action="../php/update_product_name.php" method="post">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        Название: <input id="name_products" required type="text" maxlength="50" name="name" value="<?php echo $row['name']; ?>">
                        <button type="submit">Ок</button>
                    </form>
                    <form action="../php/update_price.php" method="post">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        Цена: <input type="text" required id="price" maxlength="11" name="price" value="<?php echo $row['price']; ?>">
                        <button type="submit">Ок</button>
                    </form>
                    <form action="../php/update_image_src.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        Путь к картинке: <input required type="file" name="image">
                        <button type="submit">Ок</button>
                    </form>
                    <form action="../php/add_ingredients.php" method="post">
                        <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                        Новый ингредиент:
                        <input maxlength="30" id="ingredient" type="text" name="new_ingredient_name" required>
                        Изначально в блюде?
                        <input placeholder="Да/Нет" maxlength="3" id="isStart" type="text" name="isStart" required>
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
