<?php
require_once("config.php");
require_once("functions.php");

if(isset($_POST["register"])) {
    $errors = array();

    $firstName = clearText($_POST["firstName"]);
    $lastName = clearText($_POST["lastName"]);
    $email = clearEmail($_POST["email"]);
    $confirmEmail = clearEmail($_POST["email2"]);
    $password = clear($_POST["password"]);
    $confirmPassword = clear($_POST["password2"]);
    $phone = clear($_POST["phone"]);

    if(!empty(validateFirstName($firstName))) {
        foreach(validateFirstName($firstName) as $err) {
            $errors[] = $err;
        }    
    }

    if(!empty(validateLastName($lastName))) {
        foreach(validatelastName($lastName) as $err) {
            $errors[] = $err;
        }    
    }

    if(!empty(validateEmail($con, $email, $confirmEmail))) {
        foreach(validateEmail($con, $email, $confirmEmail) as $err) {
            $errors[] = $err;
        }    
    }

    if(!empty(validatePasswords($password, $confirmPassword))) {
        foreach(validatePasswords($password, $confirmPassword) as $err) {
            $errors[] = $err;
        }    
    }

    if(empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $query = $con->prepare("INSERT INTO users (first_name, last_name, email, pw, phone)
                                        VALUES(:fn, :ln, :em, :pw, :pp)");
        $query->bindParam(":fn", $firstName);
        $query->bindParam(":ln", $lastName);
        $query->bindParam(":em", $email);
        $query->bindParam(":pw", $hashedPassword);
        $query->bindParam(":pp", $phone);
        $query->execute();

        if($query->rowCount() == 1) {
            $_SESSION["userLoggedIn"] = $con->lastInsertId();
            $_SESSION["confirmationCode"] = rand(111111,999999);
            $messageText = "Your%20Account%20Verification%20Code%20is%20".$_SESSION["confirmationCode"];
            $msg = sendMessage($messageText, $phone);
    
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
            $errors[] = "<span class='error'>Something Went Wrong Please Try Again</span>";
        }
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
            <h1>Create A New Account</h1>
        </div>
        <?php 
            if(isset($_POST["register"])) {
                if(!empty($errors)) {
                    echo "<div class='errors'>";
                    foreach($errors as $error) {
                        echo $error;
                    }
                    echo "</div>";
                }
            } 
        ?>
        <form action="register.php" method="POST">
            <input type="text" id="firstName" class="input" name="firstName" value="<?php lastValue('firstName') ?>" placeholder="First Name" autocomplete="off" required>
            <input type="text" id="lastName" class="input" name="lastName" value="<?php lastValue('lastName') ?>" placeholder="Last Name" autocomplete="off" required>
            <input type="email" id="email" class="input" name="email" value="<?php lastValue('email') ?>" placeholder="Email" autocomplete="off" required>
            <input type="email" id="email2" class="input" name="email2" value="<?php lastValue('email') ?>" placeholder="Confirm Email" autocomplete="off" required>
            <input type="password" id="password" class="input" name="password" placeholder="Password" autocomplete="off" required>
            <input type="password" id="password2" class="input" name="password2" placeholder="Confirm Password" autocomplete="off" required>
            <input type="text" id="phone" class="input" name="phone" value="<?php lastValue('phone') ?>" placeholder="phone" autocomplete="off" required>
            <input type="submit" id="register" class="btn" name="register" value="Register">
        </form>
        <a href="login.php" class="form-text">Already Have an Account? Login here!</a>
    </div>
</body>
</html>