<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>見学ご予約フォーム</title>
</head>
<body>

<div class="wrap">
  <h2>見学ご予約フォーム</h2>

  <form method="post">
    <ul>
      <li>
        <strong>趣味</strong><br>
        <label><input type="checkbox" name="hobby[]" value="ゲーム"> ゲーム</label>
        <label><input type="checkbox" name="hobby[]" value="読書"> 読書</label>
        <label><input type="checkbox" name="hobby[]" value="映画"> 映画</label>
      </li>

      <li>
        <strong>見学の種類</strong><br>
        <label><input type="radio" name="visit" value="施設へ来場見学"> 施設へ来場見学</label>
        <label><input type="radio" name="visit" value="オンライン見学"> オンライン見学</label>
      </li>

      <li>
        <strong>希望の施設</strong><br>
        <select name="place">
          <option value="">一つ選択してください</option>
          <option value="博多展示場">博多展示場</option>
          <option value="長崎展示場">長崎展示場</option>
          <option value="熊本展示場">熊本展示場</option>
        </select>
      </li>

      <li>
        <strong>見学の希望日</strong><br>
        <select name="year">
          <option value="">年</option>
          <option>2023</option>
          <option>2024</option>
          <option>2025</option>
        </select>

        <select name="month">
          <option value="">月</option>
          <?php for($m=1;$m<=12;$m++) echo "<option>$m</option>"; ?>
        </select>

        <select name="day">
          <option value="">日</option>
          <?php for($d=1;$d<=31;$d++) echo "<option>$d</option>"; ?>
        </select>
      </li>

      <li>
        <strong>見学人数</strong><br>
        <input type="number" name="people"> 人
      </li>

      <li>
        <strong>代表者氏名</strong><br>
        <input type="text" name="sei" placeholder="姓">
        <input type="text" name="mei" placeholder="名">
      </li>

      <li>
        <strong>メールアドレス</strong><br>
        <input type="email" name="email" placeholder="xxx@example.com">
      </li>
    </ul>

    <button type="submit">登録</button>
  </form>
</div>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  echo "<hr>";
  echo "<div class='wrap'>";
  echo "<h3>📋 入力内容の確認</h3>";

  echo "趣味：";
  if (!empty($_POST["hobby"])) {
    echo implode("、", $_POST["hobby"]);
  } else {
    echo "未選択";
  }
  echo "<br>";

  echo "見学の種類：";
  echo !empty($_POST["visit"]) ? $_POST["visit"] : "未選択";
  echo "<br>";

  echo "希望の施設：";
  echo !empty($_POST["place"]) ? $_POST["place"] : "未選択";
  echo "<br>";

  echo "見学日：";
  echo $_POST["year"] . "年 " . $_POST["month"] . "月 " . $_POST["day"] . "日<br>";

  echo "見学人数：";
  echo !empty($_POST["people"]) ? $_POST["people"] . " 人" : "未入力";
  echo "<br>";

  echo "氏名：";
  echo $_POST["sei"] . " " . $_POST["mei"] . "<br>";

  echo "メール：";
  echo !empty($_POST["email"]) ? $_POST["email"] : "未入力";
  echo "<br>";

  echo "</div>";
}
?>

</body>
</html>
