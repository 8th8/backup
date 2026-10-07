<?php

include "dbconnect.php";

$student_no = $_GET["student_no"];

$sql = "DELETE FROM address WHERE student_no = :student_no";
$stmt = $dbh->prepare($sql);

$stmt->execute([
     ":student_no" => $student_no
]);

header("Location: a4_user_list.php");
exit;
