<?php

session_start();

include "dbconnect.php";


$id=$_POST["id"];

$course_id=$_POST["course_id"];



$sql="

UPDATE user_courses

SET course_id=?

WHERE id=?

AND student_no=?

";


$stmt=$dbh->prepare($sql);


$stmt->execute([

$course_id,

$id,

$_SESSION["student_no"]

]);



header("Location:a3_my_courses.php");

exit();

?>