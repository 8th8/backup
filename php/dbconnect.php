
    <?php
    $host = "localhost";
    $dbname = "webpg2";
    $user = "root";
    $pass = "";
    $dbPassword = "";

    $dbh = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",$user,$pass,
    );
    ?>
