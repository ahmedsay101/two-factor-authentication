<?php
require_once("config.php");

if(isset($_COOKIE["access_token"])) {
    $query = $con->prepare("SELECT * FROM sessions WHERE access_token=:ac");
    $query->bindParam("ac", $_COOKIE["access_token"]);
    $query->execute();
    $sqlData = $query->fetch(PDO::FETCH_ASSOC);

    if($query->rowCount() == 1) {
        $query = $con->prepare("SELECT * FROM users WHERE id=:id");
        $query->bindParam("id", $sqlData["user_id"]);
        $query->execute();
        $sqlData = $query->fetch(PDO::FETCH_ASSOC);

        echo "You Are Logged In As ".$sqlData["first_name"]." ".$sqlData["last_name"];
    }
    else {
        exit();
    }
}
else {
    exit();
}

?>