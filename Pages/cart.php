<?php
session_start();
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

    echo '<tr>';
    echo '<td>'.$product_result['name'].'</td>';
    echo '<td>'.$product_result['price'].'</td>';
    echo '<td>';
    $user_ingredients_result = $connectMySQL->query("SELECT ingredient_id FROM `carts_to_products_to_ingredients` WHERE `user_id` = '$user_id' AND `product_id` = '$product_id'");
    while ($row = $user_ingredients_result->fetch_assoc())
    {
        $ingredient_id = $row['ingredient_id'];
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
    echo '</tr>';

}
echo '</tbody>';
echo '</table>';
?>

