<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="test1.php" method="POST" >
        <h2> Teacher Form</h2>
        
        <label for="">teacher_id</label><br>
        <input type="number" name="teacher_id" required><br>

        <label for="">name</label><br>
        <input type="text" name="name" required><br>

        <label for="">password</label><br>
        <input type="password" name="password" required><br>
        
        <br> 
        <button type="submit">登録</button>
    </form>

</body>
</html>