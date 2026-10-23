
<?php

include "dbconnect.php";

// lấy student_no từ user_list.php
$student_no = $_GET["student_no"];

// Lấy thông tin sinh viên

$sql = "SELECT * FROM address WHERE student_no = ?";

$stmt = $dbh->prepare($sql);
$stmt->execute([$student_no]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Lấy môn học đã đăng ký

$sql = "SELECT courses.*FROM user_courses JOIN courses ON user_courses.course_id = courses.id WHERE user_courses.student_no = ?";

$stmt = $dbh->prepare($sql);
$stmt->execute([$student_no]);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="ja">
<link rel="stylesheet" href="user_detail.css">
<head>
    <meta charset="UTF-8">
    <title>学生情報</title>
</head>

<body>

    <h1>学生情報</h1>

    <div class="info">

        <h2>基本情報</h2>
        <p>
            <b>学生番号：</b>
            <?= htmlspecialchars($user["student_no"]) ?>
        </p>

        <p>
            <b>氏名：</b>
            <?= htmlspecialchars($user["name"]) ?>
        </p>

        <p>
            <b>学科：</b>
            <?= htmlspecialchars($user["department"]) ?>
        </p>

        <p>
            <b>学年：</b>
            <?= htmlspecialchars($user["year"]) ?>
        </p>
    </div>

    <!-- 履修科目 -->
    <h1>履修科目</h1>
    <table>

        <tr>
            <th>科目コード</th>
            <th>科目名</th>
            <th>担当教師</th>
            <th>単位</th>
            <th>曜日</th>
        </tr>

        <?php foreach ($courses as $c) { ?>
            <tr>

                <td>
                    <?= htmlspecialchars($c["course_code"]) ?>
                </td>

                <td>
                    <?= htmlspecialchars($c["course_name"]) ?>
                </td>

                <td>
                    <?= htmlspecialchars($c["teacher"]) ?>
                </td>

                <td>
                    <?= htmlspecialchars($c["credit"]) ?>
                </td>

                <td>
                    <?= htmlspecialchars($c["day"]) ?>
                </td>
            </tr>
        <?php } ?>

    </table>
    <br>

    <div align="center">
        <a href="user_list.php">
            <button>戻る</button>
        </a>
    </div>
</body>


</html>