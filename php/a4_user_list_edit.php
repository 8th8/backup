<?php
require_once "dbconnect.php";

$student_no = $_GET["student_no"];

// Lấy thông tin người dùng
$sql = "SELECT * FROM address WHERE student_no = :student_no";
$stmt = $dbh->prepare($sql);
$stmt->execute([
    ":student_no" => $student_no
]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>ユーザー編集</title>
</head>

<body>

    <h2>ユーザー情報編集</h2>

    <form action="a4_user_list_update.php" method="post">

        <input type="hidden"
            name="student_no_old"
            value="<?= $user['student_no'] ?>">

        <p>
            学科
            <input type="text"
                name="department"
                value="<?= $user['department'] ?>">
        </p>

        <p>
            学年
            <input type="number"
                name="year"
                value="<?= $user['year'] ?>">
        </p>

        <p>
            学籍番号
            <input type="text"
                name="student_no"
                value="<?= $user['student_no'] ?>">
        </p>

        <p>
            氏名
            <input type="text"
                name="name"
                value="<?= $user['name'] ?>">
        </p>

        <button type="submit">更新</button>

    </form>

</body>

</html>