<?php
//объявляем ссесию
session_start();

//подключаем базу данных
require_once("connect.php"); //$mysqli

//массив в котором будут добавляться ошибки
$errors = [];

//если клиет нажал на кнопку войти, то условие начнет выполняться
if (isset($_POST["submit"])) {

    //получаем данные из метода post
    $email = trim($_POST["email"] ?? '');
    $password = $_POST["password"] ?? '';

    //проверяем что поля все заполнены
    if (empty($email) || empty($password)) {
        array_push($errors, "Вы не заполнили все поля");
    } else {
            //проверяем валидность адреса почты
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            array_push($errors, "Адрес почты не верный");
            }
            //проверяем длину пароля
            if (mb_strlen($password, 'UTF-8') < 8) {
            array_push($errors, "Пароль должен быть не менее 8 символов");
            }
    }


    // Проверка существования email, верного пароля, если все ок редирект в фото-галерию.
    if (empty($errors)) {
        $checkEmail = "SELECT `id`, `имя`, `адрес почты`, `пароль` FROM users WHERE `адрес почты` = ? LIMIT 1";

        $stmt = $mysqli->prepare($checkEmail);   // 1. готовим запрос
        $stmt->bind_param("s", $email);        // 2. подставляем значение вместо ?
        $stmt->execute();                      // 3. выполняем
        $result = $stmt->get_result();         // 4. Получает результат из подготовленного запроса в виде объекта
        $user = $result->fetch_assoc();        // 5. fetch_assoc — Выбирает следующую строку из набора результатов и помещает её в ассоциативный массив
        $stmt->close();                       //  6. Закрывает подготовленный запрос

        if (!$user) {
            array_push($errors,'Пользователь с такой почтой не найден.');
        } elseif(!password_verify($password, $user['пароль'])) {
            array_push($errors,'Неверный пароль пользователя');
        } else {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'имя' => $user['имя'],
                'адрес почты' => $user['адрес почты'],
            ];

            session_regenerate_id(true);
            header('Location: photo-gallery.php');
            die();
        }
            
    }

} 

?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">
    <title>Войти в профиль фото-галереи</title>
</head>

<body>
    <?php if (!empty($errors)): ?>
    <?php foreach ($errors as $error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endforeach; ?>
    <?php endif; ?>
    <div class="container">
        <form action="" method="post">
            <div class="form-group">
                <input type="email" class="form-control" value="<?= htmlspecialchars($email ?? '')?>" name="email" placeholder="Введите ваш адрес почты">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="Введите ваш пароль">
            </div>
            <div class="form-btn">
                <button type="submit" class="btn btn-primary" name="submit">Войти</button>
            </div>
        </form>
    </div>
</body>

</html>