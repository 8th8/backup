<?php
require('./dbconnect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $teacher_id = $_POST['teacher_id'];
    $subject_id = $_POST['subject_id'];

    $sql = "INSERT INTO tea_subject (teacher_id, subject_id)
            VALUES (:teacher_id, :subject_id)";

    $stmt = $dbh->prepare($sql);

    $params = [
        ':teacher_id' => $teacher_id,
        ':subject_id' => $subject_id
    ];

    $stmt->execute($params);

    echo "担当科目の登録が完了しました。";
}
?>