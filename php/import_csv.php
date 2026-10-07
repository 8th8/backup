<?php

require_once 'dbconnect.php';


$file = __DIR__ . "/731.csv";


if (($handle = fopen($file, "r")) !== false) {


    $sql = "INSERT INTO address
            (student_no, name,password)
            VALUES (?, ?,?)";

    $stmt = $dbh->prepare($sql);


    while (($data = fgetcsv($handle)) !== false) {

        if (count($data) < 2) {
            continue;
        }

        $student_no = $data[0];
        $name = $data[1];
        $password = $student_no;

        $stmt->execute([
            $student_no,
            $name,
            $password
        ]);
    }

    fclose($handle);

    echo "ユーザー登録完了";
} else {
    echo "CSVファイルがありません";
}
