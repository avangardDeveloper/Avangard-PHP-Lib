<?php
require_once __DIR__ . '/../../vendor/autoload.php';

if (!empty($_POST['boxType'])) {
    $result = [];

    foreach ($_POST as $key => $value) {
        $result[ $key ] = htmlspecialchars($value);
    }

    if (!empty($_FILES)) {
        $dir = dirname( __FILE__ ) . "\\secure_folder";
        if (!is_dir($dir)) {
            mkdir($dir);
        }
        file_put_contents($dir . "\\.htaccess", "Deny from all");
        foreach ($_FILES as $key => $file) {
            $fileName = $file["name"];
            $targetPath = $dir . "\\" . $fileName;
            move_uploaded_file($file["tmp_name"], $targetPath);

            $result[ $key ] = $targetPath;
        }
    }

    echo json_encode($result);
}
