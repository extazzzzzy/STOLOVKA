<?php
session_start();

$string = "";
for ($i = 0; $i < 5; $i++) {
    $string .= chr(rand(97, 122));
}

$_SESSION['rand_code'] = $string;

$result = array_sum(array_map('ord', str_split($_SESSION['rand_code'])));
$_SESSION['result'] = $result;

$dir = "../fonts/";

$image = @imagecreatetruecolor(170, 60);
if (!$image) {
    die("Ошибка создания изображения");
}

$black = imagecolorallocate($image, 0, 0, 0);
$color = imagecolorallocate($image, 98, 78, 66);
$white = imagecolorallocate($image, 243, 146, 0);

if (!imagefilledrectangle($image, 0, 0, 399, 99, $white)) {
    die("Ошибка при отрисовке прямоугольника");
}

if (!imagettftext($image, 30, 0, 10, 40, $color, $dir . "Obitaemostrov.ttf", $_SESSION['rand_code'])) {
    die("Ошибка при добавлении текста");
}

header("Content-type: image/png");
imagepng($image);
imagedestroy($image);
