<?php
    session_start();
    require_once "pdo.php";

    if(!isset($_SESSION['name']) || strlen($_SESSION['name']) < 1)  die("ACCESS DENIED");

    if(isset($_POST['action']) && $_POST['action'] == "Cancel") {
        header('Location: index.php'); 
        return;
    }

    if(isset($_POST['action']) && isset($_POST['profile_id'])){
        $stmt = $pdo->prepare('DELETE FROM profile WHERE profile_id = :id');
        $stmt->execute(array(':id' => $_POST['profile_id']));
        $_SESSION['success'] = "Record deleted\n";
        header("Location: index.php");
        return;
    }
    
    $stmt = $pdo->prepare("SELECT * FROM profile WHERE profile_id = :id");
    $stmt->execute(array(':id' => $_GET['profile_id']));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if($row === false){
        $_SESSION['error'] = "Bad value for profile_id\n";
        header("Location: index.php");
        return;
    }
?>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Saad Makrod Delete</title>

	<link rel="stylesheet" href="../css/style.css">

    <script 
        src="https://code.jquery.com/jquery-3.2.1.js"
        integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE="
        crossorigin="anonymous">
    </script>
</head>
<body>
	<h1>Deleting Resume</h1>
		
    <p>
    <?php
            echo "Deleteing ";
            echo htmlentities($row['first_name']." ".$row['last_name']);
            echo " from the database. Are you sure?\n";
        ?> 
    </p>
    <form method="post">
        <input type="hidden" name="profile_id" value="<?=$row['profile_id']?>">
        <input type="submit" name="action" value="Delete">
        <input type="submit" name="action" value="Cancel">
    </form>
</body>
</html>