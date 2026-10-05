<?php

$host ="MySQL-8.4";
$user = "root";
$pass = "";
$database = "login_register";

$mysqli =new mysqli($host, $user, $pass, $database);
if ($mysqli->connect_error) {
    echo 'Ошибка в подключение: ' . $mysqli->connect_error;
}
?>