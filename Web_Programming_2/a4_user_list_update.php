<?php

require_once "dbconnect.php";

$sql = "UPDATE address
        SET
            department = :department,
            year = :year,
            student_no = :student_no,
            name = :name
        WHERE student_no = :student_no_old";

$stmt = $dbh->prepare($sql);

$stmt->execute([
    ":department"     => $_POST["department"],
    ":year"           => $_POST["year"],
    ":student_no"     => $_POST["student_no"],
    ":name"           => $_POST["name"],
    ":student_no_old" => $_POST["student_no_old"]
]);

header("Location: a4_user_list.php");
exit;