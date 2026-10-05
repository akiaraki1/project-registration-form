<?php
//подключаем базу данных
require_once("connect.php"); //$mysqli

//массив в котором будут добавляться ошибки
$errors = [];

//если клиет нажал на кнопку зарегистрироваться, то условие начнет выполняться
if (isset($_POST["submit"])) {

    //получаем данные из метода post
    $name = trim($_POST["name"] ?? '');
    $email = trim($_POST["email"] ?? '');
    $password = $_POST["password"] ?? '';
    $repeatPassword = $_POST["repeat_password"] ?? '';

    //проверяем что поля все заполнены
    if (empty($name) || empty($email) || empty($password) || empty($repeatPassword)) {
    $errors[] = "Вы не заполнили все поля";
    } else {
        //проверяем валидность адреса почты
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Адрес почты не верный";
        }
        //проверяем длину пароля
        if (mb_strlen($password, 'UTF-8') < 8) {
        $errors[] = "Пароль должен быть не менее 8 символов";
        }
        //проверяем совпадение паролей
        if ($password !== $repeatPassword) {
        $errors[] = "Пароли не совпадают";
        }
    }

    // Проверка существования email + безопастность
    if (empty($errors)) {
        $checkEmail = "SELECT id FROM users WHERE `адрес почты` = ? LIMIT 1";

        $stmt = $mysqli->prepare($checkEmail);   // 1. готовим запрос
        $stmt->bind_param("s", $email);        // 2. подставляем значение вместо ?
        $stmt->execute();                      // 3. выполняем
        $stmt->store_result();                 // 4. сохраняем результат, чтобы работал num_rows

        if ($stmt->num_rows > 0) {
        array_push($errors, "Адрес электронной почты уже существует");
        }

        $stmt->close();

    }

    // Если ошибок нет — регистрируем
    if (empty($errors)) {

        //хешируем пароль для безопастности
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (`имя`, `адрес почты`, `пароль`) VALUES (?, ?, ?)";
        $stmt = $mysqli->prepare($sql); // 1. готовим запрос
        $stmt->bind_param("sss", $name, $email, $hash); // 2. подставляем значение вместо ?

        // 3. выполняем
        if ($stmt->execute()) { 
            header('Location: join_profile.php');
            die();
        } else {
            error_log($stmt->error);
            array_push($errors, "Ошибка регистрации, попробуйте позже");
        }

        $stmt->close();
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
    <title>Регистрация</title>
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
                <input type="text" class="form-control" value="<?= htmlspecialchars($name ?? '')?>" name="name" placeholder="Введите ваше имя">
            </div>
            <div class="form-group">
                <input type="email" class="form-control" value="<?= htmlspecialchars($email ?? '')?>" name="email" placeholder="Введите ваш адрес почты">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="Введите ваш пароль">
            </div>
            <div class="form-group">
                <input type="password" class="form-control " name="repeat_password"
                    placeholder="Введите ваш пароль снова">
            </div>
            <div class="form-btn">
                <button type="submit" class="btn btn-primary" name="submit">Зарегистрироваться</button>
            </div>
        </form>
    </div>
</body>
</html>