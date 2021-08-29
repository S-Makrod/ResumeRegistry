<?php
    session_start();
    require_once 'pdo.php';
?>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Home</title>

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
	<h1>The Resume Registry</h1>

    <?php
        if(!isset($_SESSION['name']) || !isset($_SESSION['user_id'])){
            if(isset($_SESSION['success'])) {
                echo "<p id='insert'> ";
                echo htmlentities($_SESSION['success']); 
                echo " </p>";
                unset($_SESSION['success']);
            }
            
            $stmt = $pdo->query("SELECT first_name, last_name, headline, profile_id FROM  profile");
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if($row === false) echo "No profiles found";
            else {
                echo "<table>\n";
                echo "<tr><th>Name</th><th>Headline</th></tr>\n";
                while($row !== false){
                    echo "<tr><td>";
                    echo('<a href="view.php?profile_id='.$row['profile_id'].'">'.htmlentities($row['first_name']).' '.htmlentities($row['last_name']).'</a>');
                    echo("</td><td>");
                    echo(htmlentities($row['headline']));
                    echo("</td></tr>");
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                }
                echo "</table>\n";
            }
            echo "<p>";
            echo "<a href='login.php'>Please Sign In</a>";
            echo "</p>\n";
            echo "<p>";
            echo "<a href='signup.php'>Please Sign Up</a>";
            echo "</p>\n";
        } else {
            $stmt = $pdo->prepare("SELECT first_name, last_name, headline, profile_id FROM  profile WHERE user_id = :u");
            $stmt->execute(array(':u' => $_SESSION['user_id']));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "<p>";
            echo "Welcome ";
            echo htmlentities($_SESSION['name']);
            echo "</p>\n";

            if(isset($_SESSION['success'])) {
                echo "<p id='insert'> ";
                echo htmlentities($_SESSION['success']); 
                echo " </p>";
                unset($_SESSION['success']);
            }

            if(isset($_SESSION['error'])) {
                echo "<p id='message'> ";
                echo htmlentities($_SESSION['error']);
                echo " </p>";
                unset($_SESSION['error']);
            }

            if($row === false) echo "No profiles found";
            else {
                echo "<table>\n";
                echo "<tr><th>Name</th><th>Headline</th><th>Action</th></tr>\n";
                while($row !== false){
                    echo "<tr><td>";
                    echo('<a href="view.php?profile_id='.$row['profile_id'].'">'.htmlentities($row['first_name']).' '.htmlentities($row['last_name']).'</a>');
                    echo("</td><td>");
                    echo(htmlentities($row['headline']));
                    echo("</td><td>");
                    echo('<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a>')." ";
                    echo('<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>');
                    echo("</td></tr>\n");
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                }
                echo "</table>\n";
            }
            echo "<p>";
            echo "<a href='add.php'>Add New Entry</a> <br> <br>";
            echo "<a href='logout.php'>Logout</a>";
            echo "</p>\n";
        }
    ?>

</body>
</html>