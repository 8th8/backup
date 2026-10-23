<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List</title>
    <link rel="stylesheet" href="user_list.css">
</head>

<body>

    <header>
        <h1>🎓 ユーザー管理</h1>
        <p>User Registration System</p>
    </header>


    <div class="container">

        <aside>
            <h2>Menu</h2>

            <ul>
                <li><a href="./user_list.php">📚 User list</a></li>
                <li><a href="./a1_address_input.php">📖 ユーザー登録</a></li>
                <li><a href="./a2_login.php">📖 ユーザーログイン</a></li>
                <li><a href="./index.php">Back Home</a></li>

            </ul>

        </aside>


        <?php

        require_once "dbconnect.php";

        $sql = "SELECT * FROM address ORDER BY student_no";

        $stmt = $dbh->query($sql);

        ?>

        <table border="1">

            <tr>
                <th>学科</th>
                <th>学年</th>
                <th>学籍番号</th>
                <th>氏名</th>
                <th>編集</th>
                <th>削除</th>
            </tr>


            <?php

            foreach ($stmt as $row) {
                echo "<tr>";
                echo "<td>" . $row['department'] . "</td>";
                echo "<td>" . $row['year'] . "</td>";
                echo "<td>" . $row['student_no'] . "</td>";
                echo "<td>" . $row['name'] . "</td>";
                echo "<td>
            <a href='a4_user_list_edit.php?student_no="
                    . $row['student_no'] . "'>
            編集
            </a>
          </td>";

                echo "<td>
            <a href='a4_user_list_delete.php?student_no="
                    . $row['student_no'] . "'>
            削除
            </a>
          </td>";

                echo "</tr>";
            }
            ?>
        </table>
    </div>

    <footer>
        © 2242505 NGUYEN ANH TUAN
    </footer>


</body>

</html>