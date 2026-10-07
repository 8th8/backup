<?php

session_start();

include "dbconnect.php";


// lấy sinh viên đang đăng nhập

$student_no = $_SESSION["student_no"];


// kiểm tra có chọn môn không

if (!isset($_POST["course_id"])) {

    echo "科目を選択してください";
    exit();
}


$course_ids = $_POST["course_id"];


// lưu từng môn

foreach ($course_ids as $course_id) {


    $sql = "

    INSERT INTO user_courses(student_no, course_id)

    VALUES(?,?)

    ";


    $stmt = $dbh->prepare($sql);


    $stmt->execute([

        $student_no,

        $course_id

    ]);
}



echo "登録しました";


header("Location: a3_my_courses.php");

exit();
