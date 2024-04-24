<?php
session_start();

if ($_SESSION['role'] == ""){
    header("Location: auth.php");
    die;
}

$connectMySQL = new mysqli('localhost', 'root', 'root', 'Stolovka');

echo '<div class="table_orders">';
echo '<table>';
echo '<thead>';
echo '<tr>';
echo '<th>'."Название".'</th>';
echo '<th>'."Цена".'</th>';
echo '<th>'."Ингредиенты".'</th>';
echo '<tr>';
echo '</thead>';
echo '<tbody>';


$user_id = $_SESSION['id'];
$products_result = $connectMySQL->query("SELECT product_id FROM `carts_to_products` WHERE `user_id` = '$user_id'");

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
            echo 'Убрать: ' . $ingredient_name;
        }
        else
        {
            echo 'Добавить: ' . $ingredient_name;
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
?>

<script>
    function delete_product(form)
    {
        var productId = form.elements["product_id"].value;

        var formData = new FormData();
        formData.append('product_id', productId);

        var xhr = new XMLHttpRequest();

        xhr.open('POST', '../php/delete_product_from_cart.php', true);
        xhr.send(formData);

        var element = document.getElementById(productId);
        element.parentNode.removeChild(element);

        return false;
    }
</script>

<a href="making_an_order.php">Оформить заказ</a>

<script>
    var tdElements = document.querySelectorAll('.table_orders tbody td');
    if (tdElements.length === 0) {
        var orderLink = document.querySelector('a[href="making_an_order.php"]');
        orderLink.style.pointerEvents = 'none';
        orderLink.style.color = 'grey';
    }
</script>