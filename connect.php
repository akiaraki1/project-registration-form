<?php

$host ="MySQL-8.4";
$user = "root";
$pass = "";
$database = "login_register";

$conn=new mysqli($host, $user, $pass, $database);
if ($conn->connect_error) {
    echo 'Ошибка в подключение: ' . $mysqli->connect_error;
}
?>