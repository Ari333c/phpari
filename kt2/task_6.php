<?php
ob_start();

// Функция генерации изображения
function get_image_data() {
    if (!extension_loaded('gd')) {
        // Заглушка при отсутствии GD (прозрачный пиксель)
        return base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==');
    } else {
        $img = imagecreatetruecolor(300, 150);
        $bg = imagecolorallocate($img, 235, 235, 240);
        imagefill($img, 0, 0, $bg);

        // Красный залитый прямоугольник
        $red   = imagecolorallocate($img, 220, 50, 50);
        imagefilledrectangle($img, 20, 20, 120, 100, $red);

        // СИНИЙ ЗАЛИТЫЙ ЭЛЛИПС (центр 220,75, ширина 100, высота 60)
        $blue  = imagecolorallocate($img, 50, 80, 220);
        imagefilledellipse($img, 220, 75, 100, 60, $blue);

        // ЧЁРНЫЙ ТЕКСТ «PHP» (шрифт 5, координаты 130,65)
        $black = imagecolorallocate($img, 0, 0, 0);
        imagestring($img, 5, 130, 65, 'PHP', $black);

        // Сохраняем изображение в буфер
        ob_start();
        imagepng($img);
        $image_data = ob_get_clean();
        imagedestroy($img);
        return $image_data;
    }
}

$image_data = get_image_data();
$base64 = base64_encode($image_data);
ob_end_clean();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Задание 6: GD-изображение</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        .block { background: #fff; padding: 20px; border-radius: 8px; text-align: center; }
        img { border: 1px solid #ccc; border-radius: 4px; }
    </style>
</head>
<body>
    <h1>Задание 6: Рисование с GD</h1>
    <div class="block">
        <p>Сгенерированное PHP (GD) изображение 300×150:</p>
        <img src="data:image/png;base64,<?= $base64 ?>" alt="GD image" width="300" height="150">
    </div>
    <p><a href="task_05.php">← Задание 5</a></p>
</body>
</html>
