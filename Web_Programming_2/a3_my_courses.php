<?php

session_start();

include "dbconnect.php";
// lấy tài khoản đang đăng nhập
$student_no = $_SESSION["student_no"];
// lấy các môn mà tài khoản này đã đăng ký
$sql = "
SELECT 
    user_courses.id,
    courses.course_code,
    courses.course_name,
    courses.teacher,
    courses.credit,
    courses.day

FROM user_courses

JOIN courses

ON user_courses.course_id = courses.id

WHERE user_courses.student_no = :student_no

";


$stmt = $dbh->prepare($sql);
$stmt->execute([
    ":student_no" => $student_no
]);

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>📚 登録済み科目</h1>

<p>
    学生番号：
    <?= $_SESSION["student_no"] ?>
</p>
<p>
    氏名：
    <?= $_SESSION["name"] ?>
</p>



<table border="1">

    <tr>
        <th>コード科目</th>
        <th>科目名</th>
        <th>先生</th>
        <th>単位</th>
        <th>曜日</th>
        <th>操作</th>
    </tr>


    <?php foreach ($data as $row) { ?>


        <tr>


            <td>
                <?= $row["course_code"] ?>
            </td>


            <td>
                <?= $row["course_name"] ?>
            </td>


            <td>
                <?= $row["teacher"] ?>
            </td>


            <td>
                <?= $row["credit"] ?>
            </td>


            <td>
                <?= $row["day"] ?>
            </td>


            <td>


                <a href="a3_my_courses_edit.php?id=<?= $row['id'] ?>">
                    編集
                </a>


                <a href="a3_my_courses_delete.php?id=<?= $row['id'] ?>"
                    onclick="return confirm('この科目を削除しますか？');">

                    削除

                </a>


            </td>


        </tr>


    <?php } ?>


</table>