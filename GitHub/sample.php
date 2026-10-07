<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
    <form action="sample.php" method="post">
        <input type="text" name="user-id">
        <input type="password" name="pass" id="">
        
        <input type="submit" value="gui">

        
    </form>
    
    <?php
        $user = $_POST['user-id'];
        $password = $_POST['pass'];
        
        echo $user."---" .$password."</br>";
        ?>
</body>
</html>