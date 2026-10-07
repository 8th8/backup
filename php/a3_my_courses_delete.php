<?php

session_start();

include "dbconnect.php";


$id = $_GET["id"];


// xóa môn của đúng người đang đăng nhập

$sql = "

DELETE FROM user_courses

WHERE id = ?

AND student_no = ?

";


$stmt = $dbh->prepare($sql);


$stmt->execute([

    $id,

    $_SESSION["student_no"]

]);



header("Location: a3_my_courses.php");

exit();
