<?php
require_once("config.php");
require_once("functions.php");


if(isset($_POST["login"])) {
    $email = clearEmail($_POST["email"]);
    $password = clear($_POST["password"]);
    $errors = array();


    $query = $con->prepare("SELECT * FROM users WHERE email=:em");
    $query->bindParam("em", $email);

    $query->execute();
    $sqlData = $query->fetch(PDO::FETCH_ASSOC);
    if($query->rowCount() == 1) {
        echo $sqlData["pw"];
        if(password_verify($password, $sqlData["pw"])) {
            $_SESSION["userLoggedIn"] = $sqlData["id"];
            $_SESSION["confirmationCode"] = rand(652487,985324);;
            $messageText = "Your%20Account%20Verification%20Code%20is%20".$_SESSION["confirmationCode"];
            $msg = sendMessage($messageText, $sqlData["phone"]);
    
            if($msg["curlStatusCode"] == 201) {
                if($msg["apiStatus"] === "Success" && $msg["apiStatusCode"] == 101) {
                    header('Location: http://localhost/auth/checkpoint.php');
                    die();
                }
            }
            else {
                $errors[] = "<span class='error'>Something Went Wrong Please Try Again</span>";
            }
        }
        else {
            echo "password not";
        }

    }
    else {
        $errors[] = "<span class='error'>Your email or password was incorrect</span>";
    }
}

?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="style.css">
<link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700&display=swap" rel="stylesheet">
</head>

<body>
    <div class="form-body">
        <div class="form-header">
            <h1>Login</h1>
        </div>
        <?php 
            if(isset($_POST["login"])) {
                if(!empty($errors)) {
                    echo "<div class='errors'>";
                    foreach($errors as $error) {
                        echo $error;
                    }
                    echo "</div>";
                }
            } 
        ?>
        <form action="login.php" method="POST">
            <input type="email" id="email" class="input" name="email" value="<?php lastValue('email') ?>" placeholder="Email" autocomplete="off" required>
            <input type="password" id="password" class="input" name="password" placeholder="Password" autocomplete="off" required>
            <input type="submit" id="login" class="btn" name="login" value="Login">
        </form>
        <a href="register.php" class="form-text">Don't have an account? Create a new one here!</a>
    </div>
</body>
</html>