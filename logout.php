<?php
session_start();

 $_SESSION = [];        // очищаем все данные
session_destroy();     // уничтожаем хранилище сессии на сервере

header('Location: join_profile.php');
exit;
?>