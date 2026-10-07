<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<?php
include "dbconnect.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $student_no = $_POST["student_no"];
    $name = $_POST["name"];
    $courses = $_POST["courses"];
    $score = $_POST["score"];

    $sql = "INSERT INTO seiseiki (student_no, name, courses, score)
            VALUES (:student_no, :name, :courses, :score)";

    $stmt = $dbh->prepare($sql);

    $stmt->execute([
        ":student_no" => $student_no,
        ":name" => $name,
        ":courses" => $courses,
        ":score" => $score
    ]);

    echo "登録しました。";
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>成績登録</title>
</head>

<body>

    <h1>成績登録</h1>

    <form method="post">

        <p>
            学生番号：
            <input type="text" name="student_no" required>
        </p>

        <p>
            氏名：
            <input type="text" name="name" required>
        </p>

        <p>
            科目：
            <input type="text" name="courses" required>
        </p>

        <p>
            成績：
            <input type="number" name="score" min="0" max="100" required>
        </p>

        <button type="submit">登録</button>

    </form>

    <br>

    <a href="seiseiki.php">成績一覧を見る</a>

</body>

</html>
```

</body>
</html>