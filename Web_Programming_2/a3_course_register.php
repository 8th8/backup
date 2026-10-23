
<?php

session_start();

include "dbconnect.php";

// lấy thông tin người đăng nhập
$student_no = $_SESSION["student_no"];
$name = $_SESSION["name"];
$department = $_SESSION["department"];
$year = $_SESSION["year"];

// lấy danh sách môn

$sql = "SELECT * FROM courses";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>科目登録システム</title>
    <link rel="stylesheet" href="course_register.css">
</head>

<body>

    <header>
        <h1>🎓 科目登録システム</h1>
        <p>Course Registration System</p>
    </header>

    <div class="container">

        <aside>
            <h2>Menu</h2>

            <ul>
                <li class="active"> 科目登録</li>
                <li><a href="./a3_my_courses.php"> 登録済み科目</a></li>
                 <li><a href="./index.php">Back Home</a></li>
            </ul>
        </aside>

        <main>

            <div class="student-box">
                <h2>学生情報</h2>

                <div class="info">
                    <p>学生番号：<b><?= $_SESSION["student_no"] ?></b></p>

                    <p>氏名：<b><?= $_SESSION["name"] ?></b></p>

                    <p>学科： <b><?= $_SESSION["department"] ?></b></p>
                </div>
            </div>

            <div class="course-box">

                <h2>履修登録科目</h2>

                <form action="a3_course_register1.php" method="post">

                    <table>
                        <tr>
                            <th>選択</th>
                            <th>科目コード</th>
                            <th>科目名</th>
                            <th>担当教師</th>
                            <th>単位</th>
                            <th>曜日</th>
                        </tr>

                        <?php foreach ($courses as $c) { ?>

                            <tr>
                                <td>
                                    <input type="checkbox" name="course_id[]" value="<?= $c["id"] ?>">
                                </td>
                                <td><?= $c["course_code"] ?></td>
                                <td><?= $c["course_name"] ?></td>
                                <td><?= $c["teacher"] ?></td>
                                <td><?= $c["credit"] ?></td>
                                <td><?= $c["day"] ?></td>
                            </tr>

                        <?php } ?>
                    </table>

                    <button type="submit" class="register-btn">
                        登録する
                    </button>

                </form>

            </div>

        </main>

    </div>

    <footer>
        © 2242505 NGUYEN ANH TUAN
    </footer>

</body>

</html>