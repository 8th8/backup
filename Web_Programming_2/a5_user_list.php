<?php
include "dbconnect.php";

$sql = "SELECT * FROM address ORDER BY student_no";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>ユーザー一覧</title>

    <style>
        table {

            border-collapse: collapse;
            width: 900px;
            margin: auto;
        }

        th,
        td {

            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;

        }

        th {

            background: #3498db;
            color: white;

        }

        a {

            text-decoration: none;

        }
    </style>

</head>

<body>

    <h1 align="center">ユーザ別履修科目管理</h1>

    <table>

        <tr>

            <th>学生番号</th>
            <th>氏名</th>
            <th>学科</th>
            <th>詳細</th>

        </tr>

        <?php foreach ($users as $u) { ?>

            <tr>

                <td><?= $u["student_no"] ?></td>

                <td><?= $u["name"] ?></td>

                <td><?= $u["department"] ?></td>

                <td>

                    <a href="a5_user_detail.php?student_no=<?= $u["student_no"] ?>">
                        詳細
                    </a>

                </td>

            </tr>

        <?php } ?>

    </table>

</body>

</html>