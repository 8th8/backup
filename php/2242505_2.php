<?php
require_once 'dbconnect.php';

$course_id = $_POST['course_id'];
$score = $_POST['score'];

echo "<h2>Score</h2><br>";
echo "course_id: " . $course_id  . "<br>";
echo "score: " . $score  . "<br>";


$sql = "INSERT INTO score (course_id, score)  
                VALUES      (:course_id, :score)";
$stmt = $dbh->prepare($sql); 
$params = [   
    ':course_id' => $course_id,
    ':score' => $score,
];

$stmt->execute($params);
?>