
<?php
include "dbconnect.php";

$sql = "SELECT * FROM seiseiki ORDER BY student_no";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>履修科目</title>
</head>

<body>

    <h1 align="center">履修科目</h1>

    <table border="1">

        <tr>
            <th>学生番号</th>
            <th>氏名</th>
            <th>学科</th>
            <th>成績</th>
        </tr>

        <?php foreach ($users as $u) { ?>

            <tr>
                <td><?= $u["student_no"] ?></td>
                <td><?= $u["name"] ?></td>
                <td><?= $u["courses"] ?></td>
                <td><?= $u["score"] ?></td>
            </tr>
             <tr>
                <td><?= $u["student_no"] ?></td>
                <td><?= $u["name"] ?></td>
                <td><?= $u["courses"] ?></td>
                <td><?= $u["score"] ?></td>
            </tr>
             <tr>
                <td><?= $u["student_no"] ?></td>
                <td><?= $u["name"] ?></td>
                <td><?= $u["courses"] ?></td>
                <td><?= $u["score"] ?></td>
            </tr>

        <?php } ?>

    </table>

</body>

</html>

