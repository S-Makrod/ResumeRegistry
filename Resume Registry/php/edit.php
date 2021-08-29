<?php
    session_start();
    require_once "pdo.php";

    if(!isset($_SESSION['name']) || strlen($_SESSION['name']) < 1)  die("ACCESS DENIED");

    if(isset($_POST['action']) && $_POST['action'] == "Cancel") {
        header('Location: index.php'); 
        return;
    }

    if(isset($_POST['action']) && isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary']) && isset($_POST['profile_id'])){
        $first = $_POST['first_name'];
        $last = $_POST['last_name'];
        $summary = $_POST['summary'];
        $headline = $_POST['headline'];
        $email = $_POST['email'];
        $msg1 = validatePositions();
        $msg2 = validateEducation();

        if(strlen($first) < 1 || strlen($last) < 1 || strlen($email) < 1 || strlen($summary) < 1 || strlen($headline) < 1) {
            $_SESSION['error'] = "All fields are required\n";
            header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
            return;
        } else if(strpos($email, 'a') === false) {
            $_SESSION['error'] = "Email address must contain @\n";
            header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
            return;
        } else if(is_string($msg1)){
            $_SESSION['error'] = $msg1;
            header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
            return;   
        } else if(is_string($msg2)){
            $_SESSION['error'] = $msg2;
            header("Location: add.php");
            return;   
        } else {
            $stmt = $pdo->prepare('UPDATE profile SET first_name = :f, last_name = :l, headline = :h, email = :e, summary = :s WHERE profile_id = :id');
            $stmt->execute(array(':f' => $first, ':l' => $last, ':h' => $headline, ':e' => $email, ':s' => $summary, ':id' => $_GET['profile_id']));

            $stmt = $pdo->prepare('DELETE FROM position WHERE profile_id=:pid');
            $stmt->execute(array( ':pid' => $_GET['profile_id']));

            $rank = 1;
            for($i = 1; $i <= 9; $i++){
                if (!isset($_POST['pos_year'.$i])) continue;
                if (!isset($_POST['desc'.$i])) continue;
                $year = $_POST['pos_year'.$i];
                $desc = $_POST['desc'.$i];
                $stmt = $pdo->prepare('INSERT INTO position (profile_id, rank, year, description) VALUES (:profile_id, :rank, :year, :description)');
                $stmt->execute(array(':profile_id' => $_GET['profile_id'], ':rank' => $rank, ':year' => $year, ':description' => $desc));
                $rank++;
            }

            $stmt = $pdo->prepare('DELETE FROM education WHERE profile_id=:pid');
            $stmt->execute(array( ':pid' => $_GET['profile_id']));

            $rank = 1;
            for($i = 1; $i <= 9; $i++){
                if (!isset($_POST['edu_year'.$i])) continue;
                if (!isset($_POST['inst'.$i])) continue;

                $year = $_POST['edu_year'.$i];
                $inst = $_POST['inst'.$i];

                $stmt = $pdo->prepare('SELECT * FROM institution WHERE name = :name');
                $stmt->execute(array(':name' => $inst));
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if($row === false){
                    $stmt = $pdo->prepare('INSERT INTO institution (name) VALUES (:name)');
                    $stmt->execute(array(':name' => $inst));
                    $inst_id = $pdo->lastInsertId();
                } else {
                    $inst_id = $row['institution_id'];
                }

                $stmt = $pdo->prepare('INSERT INTO education (profile_id, institution_id, rank, year) VALUES (:profile_id, :institution_id, :rank, :year)');
                $stmt->execute(array('profile_id' => $_GET['profile_id'], ':institution_id' => $inst_id, ':rank' => $rank, ':year' => $year));

                $rank++;
            }

            $_SESSION['success'] = "Record updated\n";
            header("Location: index.php");
            return;
        }
    }
    
    $stmt = $pdo->prepare("SELECT * FROM profile WHERE profile_id = :id AND user_id = :u_id");
    $stmt->execute(array(':id' => $_GET['profile_id'], ':u_id' => $_SESSION['user_id']));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if($row === false){
        $_SESSION['error'] = "Bad value for profile_id\n";
        header("Location: index.php");
        return;
    }

    function validatePositions() {
        for($i=1; $i<=9; $i++) {
            if (!isset($_POST['pos_year'.$i])) continue;
            if (!isset($_POST['desc'.$i])) continue;
            $year = $_POST['pos_year'.$i];
            $desc = $_POST['desc'.$i];
            if (strlen($year) == 0 || strlen($desc) == 0) return "All fields are required";
            if (!is_numeric($year)) return "Year must be numeric";
        }
        return true;
    }

    function validateEducation() {
        for($i=1; $i<=9; $i++) {
            if (!isset($_POST['edu_year'.$i])) continue;
            if (!isset($_POST['inst'.$i])) continue;
            $year = $_POST['edu_year'.$i];
            $inst = $_POST['inst'.$i];
            if (strlen($year) == 0 || strlen($inst) == 0) return "All fields are required\n";
            if (!is_numeric($year)) return "Year must be numeric\n";
        }
        return true;
    }
?>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Edit Profile</title>

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
	<h1>Editing Profile</h1>

    <?php
        if(isset($_SESSION['error'])) {
            echo "<p id='message'> ";
            echo htmlentities($_SESSION['error']);
            echo " </p>";
            unset($_SESSION['error']);
        }
    ?>
		
    <form method="post">
        First Name: <input type="text" id="first_name" name="first_name" value="<?= htmlentities($row['first_name'])?>"> <br>
        Last Name: <input type="text" id="last_name" name="last_name" value="<?= htmlentities($row['last_name'])?>"> <br>
        Email: <input type="text" id="email" name="email" value="<?= htmlentities($row['email'])?>"> <br>
        Headline: <br> <input type="text" id="headline" name="headline" value="<?= htmlentities($row['headline'])?>"> <br>
        Summary: <br> <textarea type="text" id="summary" name="summary" > <?php echo htmlentities($row['summary']); ?></textarea> <br>
        Education: <input type="submit" id="addEdu" value="+"> <br>
        <div id="edu_fields">
            <?php
                $rankEdu = 1;
                
                for($i = 1; $i <= 9; $i++){
                    $stmt = $pdo->prepare("SELECT * FROM education WHERE profile_id = :id AND rank = :r");
                    $stmt->execute(array(':id' => $_GET['profile_id'], ':r' => $rankEdu));
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
                    if ($row === false ) continue;

                    $year = $row['year'];

                    $stmt = $pdo->prepare("SELECT * FROM institution WHERE institution_id = :id");
                    $stmt->execute(array(':id' => $row['institution_id']));
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
                    echo '<div id="education'.$rankEdu.'"> 
                        <p>Year: <input type="text" name="edu_year'.$rankEdu.'" value="'.htmlentities($year).'" /> 
                        <input type="button" value="-" 
                        onclick="$(\'#education'.$rankEdu.'\').remove();return false;"></p> 
                        <p>School: <input type="text" name="inst'.$rankEdu.'" value="'.htmlentities($row['name']).'" class="school"></p>
                        </div>';
                    $rankEdu++;
                }
            ?>
        </div> <br>
        Position: <input type="submit" id="addPos" value="+"> 
        <div id="positions">
            <?php
                $rankPos = 1;
                
                for($i = 1; $i <= 9; $i++){
                    $stmt = $pdo->prepare("SELECT * FROM position WHERE profile_id = :id AND rank = :r");
                    $stmt->execute(array(':id' => $_GET['profile_id'], ':r' => $rankPos));
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
                    if ($row === false ) continue;
        
                    echo '<div id="position'.$rankPos.'">
                        <p>Year: <input type="text" name="pos_year'.$rankPos.'" value="'.htmlentities($row['year']).'" />
                        <input type="button" value="-"
                        onclick="$(\'#position'.$rankPos.'\').remove();return false;"></p>
                        <textarea name="desc'.$rankPos.'" rows="8" cols="80" style="font-family: Arial, Helvetica, sans-serif;">'.htmlentities($row['description']).'</textarea>
                        </div>';
                    $rankPos++;
                }
            ?>
        </div> <br>
        <input type="hidden" name="profile_id" value="<?=$row['profile_id']?>">
        <input type="submit" name="action" value="Save">
        <input type="submit" name="action" value="Cancel">
    </form>

    <script>
        countPos = <?= $rankPos ?>;
        countEdu = <?= $rankEdu ?>;
        $(document).ready(function(){
            window.console && console.log('Document ready called');
            $('#addPos').click(function(event){
                event.preventDefault();
                if ( countPos > 9 ) {
                    alert("Maximum of nine changes allowed at once!");
                    return;
                }
                
                window.console && console.log("Adding position number " + countPos);
                $('#positions').append(
                    '<div id="position'+countPos+'"> \
                    <p>Year: <input type="text" name="pos_year'+countPos+'" value="" /> \
                    <input type="button" value="-" \
                    onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
                    <textarea name="desc'+countPos+'" rows="8" cols="80" style="font-family: Arial, Helvetica, sans-serif;"></textarea>\
                    </div>'
                );

                countPos++;
            });

            $('#addEdu').click(function(event){
                event.preventDefault();
                if ( countEdu > 9 ) {
                    alert("Maximum of nine education entries exceeded");
                    return;
                }
                
                window.console && console.log("Adding education "+countEdu);

                $('#edu_fields').append(
                    '<div id="education'+countEdu+'"> \
                    <p>Year: <input type="text" name="edu_year'+countEdu+'" value="" /> \
                    <input type="button" value="-" \
                    onclick="$(\'#education'+countEdu+'\').remove();return false;"></p> \
                    <p>School: <input type="text" name="inst'+countEdu+'" value="" class="school"></p>\
                    </div>'
                );

                countEdu++;

                $('.school').autocomplete({
                    source: "school.php"
                });
            });

            $('.school').autocomplete({
                    source: "school.php"
            });
        });
    </script>
</body>
</html>