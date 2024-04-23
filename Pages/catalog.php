<?php
session_start();
if (!isset($_SESSION['id']))
{
    header("Location: auth.php");
    exit();
}
?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <style>
            /* Стили для модального окна */
            .modal {
                display: none; /* По умолчанию модальное окно скрыто */
                position: fixed; /* Фиксированное положение модального окна */
                z-index: 1; /* Над всеми остальными элементами */
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                overflow: auto; /* Добавление прокрутки, если контент не помещается */
                background-color: rgba(0,0,0,0.4); /* Черный цвет с небольшой прозрачностью */
            }

            /* Стили для контента модального окна */
            .modal-content {
                background-color: #fefefe;
                margin: 15% auto; /* 15% от верха и по центру */
                padding: 20px;
                border: 1px solid #888;
                width: 80%; /* Ширина контента */
                max-width: 600px; /* Максимальная ширина контента */
                border-radius: 8px;
                position: relative;
            }

            /* Стили для кнопки закрытия модального окна */
            .close {
                color: #aaa;
                float: right;
                font-size: 28px;
                font-weight: bold;
            }

            /* Стили для кнопки закрытия при наведении курсора */
            .close:hover,
            .close:focus {
                color: black;
                text-decoration: none;
                cursor: pointer;
            }
        </style>
    </head>
    <body>
    <main>
        <div>
            <?php
            $connectMySQL = new mysqli('localhost', 'root', 'root', 'Stolovka');
            $result = $connectMySQL->query("SELECT * FROM products");

            while ($row = $result->fetch_assoc())
            {
                echo '<div>';
                // Внесены изменения в onclick, добавлен вызов функции showModal() с передачей идентификатора продукта
                echo '<img class="product" src="..' . $row['image_src']. '" alt="Тут должно быть фото" style="width: 200px" style="height: 200px" onclick="showModalPhp(' . $row['id'] . ')">';
                echo '<br>';
                echo $row['name'];
                echo '<br>';
                echo $row['price'] . " руб.";
                echo '<br>';
                echo '<div>';

                $temp = $row['id'];
                $result2 = $connectMySQL->query("SELECT ingredient_id, is_included FROM products_to_ingredients WHERE product_id = '$temp'");
                echo '<form action="add_product.php" method="post">';
                while ($row2 = $result2->fetch_assoc())
                {
                    $temp2 = $row2['ingredient_id'];
                    $temp3 = $row2['is_included'];
                    $result3 = $connectMySQL->query("SELECT name FROM ingredients WHERE id = '$temp2'");

                    echo '    <input type="hidden" name="review_id" value="' . $row['id'] . '">';
                    echo '    <button type="submit" class="delete_review_btn">Удалить отзыв</button>';

                    echo $result3->fetch_assoc()['name'];
                    echo '<input type="checkbox" value="' . $temp3 . '" ' . ($temp3 == 1 ? 'checked' : '') . '>';
                    echo '<br>';
                }
                echo '</form>';

                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>
    </main>
    </body>
    </html>
<?php

?>