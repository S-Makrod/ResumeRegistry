<?php
    session_start();
    require_once "pdo.php";

    if(!isset($_GET['term'])) die('Mising Parameter Required');

    if(!isset($_SESSION['name'])) die("Must be logged in");
    
    header("Content-type: application/json; charset = utf-8");

    $term = $_GET['term'];

    $stmt = $pdo->prepare('SELECT name FROM institution WHERE name LIKE :prefix');
    $stmt->execute(array( ':prefix' => $term."%"));

    $retval = array();

    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) $retval[] = $row['name'];

    echo(json_encode($retval, JSON_PRETTY_PRINT));
?>