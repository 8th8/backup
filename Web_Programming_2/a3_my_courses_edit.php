<?php

session_start();

include "dbconnect.php";


$id=$_GET["id"];



$sql="

SELECT *

FROM user_courses

JOIN courses

ON user_courses.course_id=courses.id

WHERE user_courses.id=?

AND user_courses.student_no=?

";


$stmt=$dbh->prepare($sql);

$stmt->execute([

$id,

$_SESSION["student_no"]

]);


$data=$stmt->fetch(PDO::FETCH_ASSOC);



?>


<h2>科目編集</h2>


<p>
現在：
<?= $data["course_name"] ?>
</p>


<form action="a3_my_courses_update.php" method="post">


<input type="hidden" name="id" value="<?= $id ?>">


<select name="course_id">


<?php

$sql="SELECT * FROM courses";

$stmt=$dbh->query($sql);


foreach($stmt as $c){

?>


<option value="<?= $c["id"] ?>">

<?= $c["course_name"] ?>

</option>


<?php } ?>


</select>



<button type="submit">

更新

</button>


</form>