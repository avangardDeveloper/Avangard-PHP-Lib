<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use GenerateBox\GenerateBoxController;

$boxData = json_encode(GenerateBoxController::getBoxes());
?><!DOCTYPE html>
<html lang="ru">
<head>
    <title>Генерация авторизационных данных касс</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
    <link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="css/util.css">
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <!--===============================================================================================-->
</head>
<body>
<div class="limiter">
    <div class="container-login100">
        <span class="login100-form-title p-b-70">Генерация авторизационных данных касс</span>
        <div id="app" class="wrap-login100 p-b-20"></div>
    </div>
</div>
<script type="module">
    import MagicForm from "./js/main.js";

    const magicForm = new MagicForm(<?=$boxData?>);
    document.querySelector('#app').append(magicForm.element);
</script>
</body>
</html>