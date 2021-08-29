<?php
    session_start();
    require_once "pdo.php";

    if (isset($_POST['pass']) && isset($_POST['email'])){
        $salt = 'XyZzy12*_';
        $check = hash('md5', $salt.$_POST['pass']);
        $stmt = $pdo->prepare('SELECT user_id, name FROM users WHERE email = :em AND password = :pw');
        $stmt->execute(array( ':em' => $_POST['email'], ':pw' => $check));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row !== false) {
            $_SESSION['name'] = $row['name'];
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['success'] = "Successful Login\n";
            header("Location: index.php");
            return;
        } else {
            $_SESSION['error'] = "Invalid Credentials!\n";
            header("Location: login.php");
            return;
        }
    }
?>
<script>
    function doValidate() {
        console.log('Validating...');
        try {
            pw = document.getElementById('pass').value;
            console.log("Validating pw="+pw);
            if (pw == null || pw == "") {
                alert("Both fields must be filled out");
                return false;
            }

            us = document.getElementById('email').value;
            console.log("Validating us="+us);
            if (us == null || us == "") {
                alert("Both fields must be filled out");
                return false;
            } else if (us.includes("@") == false) {
                alert("Invalid email address");
                return false;
            }

            return true;
        } catch(e) {
            return false;
        }
        return false;
    }
</script>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Sign In</title>

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
	<h1>Please Sign In</h1>

    <p>Enter your username and password</p>
    
    <?php
        if(isset($_SESSION['error'])){
            echo "<p id='message'> ";
            echo htmlentities($_SESSION['error']);
            echo " </p>";
            unset($_SESSION['error']);
        }
    ?>
    
	<!-- Two accounts
        Account 1: username: 'saad.makrod@gmail.com' password: 'securepassword:)'
        Account 2: username: 'jack@outlook.ca' password: 'php123' -->
    <form method="post">
        Username <input type="text" name="email" id="email"><br/>
        Password <input type="password" name="pass" id="pass"><br/> 
        <input type="submit" onclick="return doValidate();" value="Log In">
        <input type="button" onclick="location.href='index.php'; return false;" value="Cancel">
    </form>
</body>
</html>