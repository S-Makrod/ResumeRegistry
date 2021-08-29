<?php
    $pdo = new PDO('mysql:host=localhost;port=3306;dbname=profile', 'fred', 'zap');
    // $pdo = new PDO('mysql:host=localhost;port=3306;dbname=profile', 'Saad', 'saad123'); ALTERNATE
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>