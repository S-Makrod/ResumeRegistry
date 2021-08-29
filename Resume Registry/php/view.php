<?php
    session_start();
    require_once "pdo.php";
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
	<title>View Profile</title>

	<link rel="stylesheet" href="../css/style.css">

    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css">

    <script
        src="https://code.jquery.com/jquery-3.2.1.js"
        integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE="
        crossorigin="anonymous">
    </script>

    <script
        src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"
        integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30="
        crossorigin="anonymous">
    </script>
</head>
<body>
 	<h1>Viewing Profile</h1>
	
    <p>
        <?php
            echo "First Name: ".$row['first_name']."<br><br>\n";
            echo "Last Name: ".$row['last_name']."<br><br>\n";
            echo "Email: ".$row['email']."<br><br>\n";
            echo "Headline:<br>".$row['headline']."<br><br>\n";
            echo "Summary:<br>".$row['summary']."<br><br>";

            echo "Education:<br>";

            $rank = 1;
        
            echo "<ul>";
            for($i = 1; $i <= 9; $i++){
                $stmt = $pdo->prepare("SELECT * FROM education WHERE profile_id = :id AND rank = :r");
                $stmt->execute(array(':id' => $_GET['profile_id'], ':r' => $rank));
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($row === false ) continue;

                $year = $row['year'];

                $stmt = $pdo->prepare("SELECT * FROM institution WHERE institution_id = :id");
                $stmt->execute(array(':id' => $row['institution_id']));
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                echo "<li>".$year.": ".$row['name']."</li>\n";

                $rank++;
            }
            echo "</ul> <br>";

            echo "Positions:<br>";
            
            $rank = 1;
        
            echo "<ul>";
            for($i = 1; $i <= 9; $i++){
                $stmt = $pdo->prepare("SELECT * FROM position WHERE profile_id = :id AND rank = :r");
                $stmt->execute(array(':id' => $_GET['profile_id'], ':r' => $rank));
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($row === false ) continue;

                echo "<li>".$row['year'].": ".$row['description']."</li>\n";

                $rank++;
            }
            echo "</ul> <br>";
        ?>
    </p>

    <a href="index.php">Back</a>

</body>
</html>