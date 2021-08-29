<?php
    session_start();
    require_once 'pdo.php';

    if (isset($_POST['pass']) && isset($_POST['email']) && isset($_POST['name'])){
        $salt = 'XyZzy12*_';
        $pass = hash('md5', $salt.$_POST['pass']);
        $stmt = $pdo->prepare('INSERT INTO users(name, email, password) VALUES (:n, :e, :p)');
        $stmt->execute(array( ':n' => $_POST['name'], ':e' => $_POST['email'], ':p' => $pass));

        $_SESSION['success'] = "Successful Sign Up\n";
        header("Location: index.php");
        return;
    }
?>
<script>
    function doValidate() {
        console.log('Validating...');
        try {
            pw = document.getElementById('pass').value;
            console.log("Validating pw="+pw);
            if (pw == null || pw == "") {
                alert("All fields must be filled out");
                return false;
            }

            us = document.getElementById('email').value;
            console.log("Validating us="+us);
            if (us == null || us == "") {
                alert("All fields must be filled out");
                return false;
            } else if (us.includes("@") == false) {
                alert("Invalid email address, please include '@' in username");
                return false;
            }

            nm = document.getElementById('name').value;
            console.log("Validating nm="+nm);
            if (nm == null || nm == "") {
                alert("All fields must be filled out");
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
	<title>Sign Up</title>

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

    <link rel="stylesheet" href="../css/style.css?<?php echo time(); ?>">
</head>
<body>
	<h1>Please Sign Up</h1>

    <p>Enter your name, username and password</p>
    
    <form method="post">
        Name <input type="text" name="name" id="name"><br/>
        Username <input type="text" name="email" id="email"><br/>
        Password <input type="password" name="pass" id="pass"><br/> 
        <input type="submit" onclick="return doValidate();" value="Sign Up">
        <input type="button" onclick="location.href='index.php'; return false;" value="Cancel">
    </form>
</body>
</html>