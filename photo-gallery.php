<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: join_profile.php');
    die();
}
//проверяем существования файла и на отсутствие ошибок
if (isset($_FILES['picture']) && $_FILES['picture']['error'] === 0) {

    // basename - защита от нападения, убирает слеши в тексте. 
    $name = basename($_FILES['picture']['name']);


    // Получаем расширение файла в нижнем регистре
    $ext  = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];


    //проверка на существование нужного расширения
    if (in_array($ext, $allowed, true)) {

        //Перемещает загруженный файл в новое место
        $newName = bin2hex(random_bytes(8)) . '.' . $ext;   // генерируем имя файлу
        move_uploaded_file($_FILES['picture']['tmp_name'], __DIR__ . "/image/$newName");

        //редирект
        header('Location: photo-gallery.php');
        exit;
    }
}
 ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Фото галерея</title>
    <style>
        * {
            box-sizing: border-box;
        }

        img {
            width: 100px;
            height: 100px;
            border-radius: 10px;
        }

        div {
            width: 800px;
            display: grid;
            grid-template-columns: repeat(4, 150px);
            justify-content: space-evenly;
            gap: 20px;
            margin: auto;
            background-color: #E8F4F8;
            padding: 15px;
            align-items: center;
            border-radius: 20px;
            justify-items: center;
        }

        form {
            width: auto;
            margin: 10px;
            border: solid 1px black;
            display: inline-block;
        }
        h1 {
            text-align: center;
        }
        a {
            text-decoration: none;
            color: black;
        }
    </style>
</head>

<body>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="file" name="picture">
        <button type="submit">Отправить</button>
        <button type="button"><a href="logout.php">Выйти из профиля</a></button>
    </form>
    <h1>Фото галерея</h1>
    <div>
        <?php
        //Получает список файлов и каталогов, расположенных по указанному пути возвращает массив
        $arr_imgs = scandir(__DIR__ . '/image');

        foreach ($arr_imgs as $file):

            if (!in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp'], true)) {
            continue;
            }

            ?>
            <img src="image/<?= htmlspecialchars($file); ?>" alt="<?= htmlspecialchars($file); ?>">
        <?php endforeach; ?>
    </div>
</body>

</html>