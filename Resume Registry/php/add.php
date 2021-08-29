<?php
    session_start();
    require_once "pdo.php";
    $first = "";
    $last = "";
    $headline = "";
    $summary = "";
    $email = "";

    if(!isset($_SESSION['name']) || strlen($_SESSION['name']) < 1)  die("ACCESS DENIED");

    if(isset($_POST['action']) && $_POST['action'] == "Cancel") {
        unset($_SESSION['first_name']);
        unset($_SESSION['last_name']);
        unset($_SESSION['headline']);
        unset($_SESSION['summary']);
        unset($_SESSION['email']);
        header('Location: index.php'); 
        return;
    }

    if(isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['headline']) && isset($_POST['summary']) && isset($_POST['email'])){       
        $first = $_POST['first_name'];
        $last = $_POST['last_name'];
        $summary = $_POST['summary'];
        $headline = $_POST['headline'];
        $email = $_POST['email'];
        $_SESSION['first_name'] = $first;
        $_SESSION['last_name'] = $last;
        $_SESSION['email'] = $email;
        $_SESSION['headline'] = $headline;
        $_SESSION['summary'] = $summary;
        $msg1 = validatePositions();
        $msg2 = validateEducation();

        if(strlen($first) < 1 || strlen($last) < 1 || strlen($email) < 1 || strlen($summary) < 1 || strlen($headline) < 1) {
            $_SESSION['error'] = "All fields are required\n";
            header("Location: add.php");
            return;
        } else if(strpos($email, '@') === false) {
            $_SESSION['error'] = "Email address must contain @\n";
            header("Location: add.php");
            return;
        } else if(is_string($msg1)){
            $_SESSION['error'] = $msg1;
            header("Location: add.php");
            return;   
        } else if(is_string($msg2)){
            $_SESSION['error'] = $msg2;
            header("Location: add.php");
            return;   
        } else {
            $stmt = $pdo->prepare('INSERT INTO profile(first_name, last_name, email, headline, summary, user_id) VALUES (:f, :l, :e, :h, :s, :u)');
            $stmt->execute(array(':f' => $first, ':l' => $last, ':e' => $email, ':h' => $headline, ':s' => $summary, ':u' => $_SESSION['user_id']));
            $profile_id = $pdo->lastInsertId();
            
            $rank = 1;
            for($i = 1; $i <= 9; $i++){
                if (!isset($_POST['pos_year'.$i])) continue;
                if (!isset($_POST['desc'.$i])) continue;

                $year = $_POST['pos_year'.$i];
                $desc = $_POST['desc'.$i];
                $stmt = $pdo->prepare('INSERT INTO position(profile_id, rank, year, description) VALUES (:profile_id, :rank, :year, :description)');
                $stmt->execute(array(':profile_id' => $profile_id, ':rank' => $rank, ':year' => $year, ':description' => $desc));
                $rank++;
            }

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
                $stmt->execute(array('profile_id' => $profile_id, ':institution_id' => $inst_id, ':rank' => $rank, ':year' => $year));

                $rank++;
            }
            
            $_SESSION['success'] = "Record added\n";
            unset($_SESSION['first_name']);
            unset($_SESSION['last_name']);
            unset($_SESSION['headline']);
            unset($_SESSION['summary']);
            unset($_SESSION['email']);
            header("Location: index.php");
            return;
        }
    }

    function validatePositions() {
        for($i=1; $i<=9; $i++) {
            if (!isset($_POST['pos_year'.$i])) continue;
            if (!isset($_POST['desc'.$i])) continue;
            $year = $_POST['pos_year'.$i];
            $desc = $_POST['desc'.$i];
            if (strlen($year) == 0 || strlen($desc) == 0) return "All fields are required\n";
            if (!is_numeric($year)) return "Year must be numeric\n";
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
	<title>Add Profile</title>

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
 	<h1>Adding Profile</h1>

    <?php
        if(isset($_SESSION['error'])) {
            echo "<p id='message'> ";
            echo htmlentities($_SESSION['error']);
            echo " </p>";
            unset($_SESSION['error']);
        }
    ?>

    <?php
        if(isset($_SESSION['first_name'])) $first = $_SESSION['first_name'];
        if(isset($_SESSION['last_name'])) $last = $_SESSION['last_name'];
        if(isset($_SESSION['email'])) $email = $_SESSION['email'];
        if(isset($_SESSION['headline'])) $headline = $_SESSION['headline'];
        if(isset($_SESSION['summary'])) $summary = $_SESSION['summary'];
    ?>
		
    <form method="post">
        First Name: <input type="text" id="first_name" name="first_name" value="<?= htmlentities($first)?>"> <br>
        Last Name: <input type="text" id="last_name" name="last_name" value="<?= htmlentities($last)?>"> <br>
        Email: <input type="text" id="email" name="email" value="<?= htmlentities($email)?>"> <br>
        Headline: <br> <input type="text" id="headline" name="headline" value="<?= htmlentities($headline)?>"> <br>
        Summary: <br> <textarea type="text" id="summary" name="summary" value="<?= htmlentities($summary)?>"></textarea> <br>
        Education: <input type="submit" id="addEdu" value="+"> <br>
        <div id="edu_fields"></div> <br>
        Position: <input type="submit" id="addPos" value="+"> <br>
        <div id="positions"></div> <br>
        <input type="submit" name="action" value="Add" id="add">
        <input type="submit" name="action" value="Cancel" id="cancel">
    </form> 

    <script> 
        countPos = 1;
        countEdu = 1;
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
        });
    </script>

</body>
</html>