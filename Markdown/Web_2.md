# PHP & MySQL Notes

> Notes môn Web Programming
> Database: `webpg2`

---

# 4月22日 — Session + MySQL

## 1. PHP Session + MySQL

### Mục đích

Sử dụng **PHP Session + MySQL** để:

1. Lấy ID của user đang đăng nhập.
2. Kiểm tra user đã đăng nhập chưa.
3. Lấy thông tin user từ database.
4. Hiển thị thông tin lên form.
5. Cho phép user sửa thông tin.

---

## 2. Lấy `user_id` từ Session

```php
<?php

session_start();

$user_id = $_SESSION['user_id'];

if (!$user_id) {
    header('Location: user_register_error.php');
}

?>
```

### Giải thích

```php
session_start();
```

→ Khởi động Session.

```php
$_SESSION['user_id'];
```

→ Lấy `user_id` đã được lưu trong Session.

```php
if (!$user_id)
```

→ Nếu chưa có user ID thì chuyển sang trang lỗi.

---

## 3. Lấy thông tin User từ MySQL

```php
require('./dbconnect.php');

$sql = "SELECT * FROM user WHERE id = '$user_id'";

$stmt = $dbh->query($sql);

foreach ($stmt as $row) {

    $name = $row['name'];
    $email = $row['email'];
    $phone = $row['phone'];
    $wage = $row['hourly_wage'];
    $trans = $row['trans_expense'];
    $insur = $row['insurance'];

}
```

### Các dữ liệu lấy được

| Database column | PHP variable |
| --------------- | ------------ |
| `name`          | `$name`      |
| `email`         | `$email`     |
| `phone`         | `$phone`     |
| `hourly_wage`   | `$wage`      |
| `trans_expense` | `$trans`     |
| `insurance`     | `$insur`     |

---

# 4. Hiển thị dữ liệu trong Form

```html
<form class="login-form" action="user_update.php" method="POST">

    <div class="login-username">
        <span>氏名</span>
        <input type="text" name="name" value="<?php echo $name; ?>">
    </div>

    <div class="login-username">
        <span>Email</span>
        <input type="email" name="email" value="<?php echo $email; ?>">
    </div>

    <div class="login-username">
        <span>電話番号</span>
        <input type="tel"
               name="phone"
               value="<?php echo $phone; ?>">
    </div>

</form>
```

### Điểm quan trọng

```php
value="<?php echo $name; ?>"
```

→ Hiển thị dữ liệu lấy từ database vào `<input>`.

---

# 5. Hiển thị dữ liệu không cho sửa

Ví dụ:

```php
<span>時給</span>
<?php echo $wage . " 円"; ?>
```

Hoặc:

```php
<span>交通費</span>
<?php echo $trans . " 円"; ?>
```

---

# 6. Form gửi dữ liệu cập nhật

```html
<form action="user_update.php" method="POST">
```

Khi nhấn nút:

```html
<input type="submit" value="修正・確認">
```

→ Dữ liệu được gửi đến:

```text
user_update.php
```

---

# 5月13日 — Database & User Registration

# 7. Database

```text
Database name:
webpg2
```

---

# 8. Table `address`

| ID | Column     | Type         |
| -: | ---------- | ------------ |
|  1 | `id`       | int(11)      |
|  2 | `std_no`   | int(11)      |
|  3 | `std_name` | varchar(50)  |
|  4 | `password` | varchar(255) |
|  5 | `date`     | timestamp    |

---

# 9. Table `subject`

| ID | Column         | Type        |
| -: | -------------- | ----------- |
|  1 | `id`           | int(11)     |
|  2 | `subject_name` | varchar(50) |

---

# 10. `dbconnect.php`

File này dùng để **kết nối PHP với MySQL**.

```php
<?php

define('DB_HOST', 'localhost');
define('DB_NAME', 'webpg2');
define('DB_USER', 'root');
define('DB_PASSWORD', '');

$options = array(
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET 'utf8'"
);

error_reporting(E_ALL & ~E_NOTICE);

try {

    $dbh = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME,
        DB_USER,
        DB_PASSWORD,
        $options
    );

    $dbh->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

} catch (PDOException $e) {

    echo $e->getMessage();
    exit;

}

date_default_timezone_set('Asia/Tokyo');

?>
```

---

# 11. User Registration — `address_input.php`

```html
<!DOCTYPE html>
<html lang="ja">

<body>

<h2>ユーザ登録 (Registration Form)</h2>

<form method="post" action="address_input_1.php">

    <p>
        学籍番号 (Student No): <br>
        <input type="number" name="std_no" required>
    </p>

    <p>
        氏名 (Name): <br>
        <input type="text" name="std_name" required>
    </p>

    <p>
        パスワード (Password): <br>
        <input type="password" name="password" required>
    </p>

    <button type="submit">登録 (Register)</button>

</form>

</body>
</html>
```

---

# 12. Nhận dữ liệu Form — `$_POST`

```php
$no = $_POST['std_no'];
$name = $_POST['std_name'];
$pass = $_POST['password'];
```

| PHP     | Dữ liệu    |
| ------- | ---------- |
| `$no`   | Student No |
| `$name` | Tên        |
| `$pass` | Password   |

---

# 13. INSERT bằng PDO

```php
$sql = "INSERT INTO address
        (std_no, std_name, password, date)
        VALUES
        (:s_no, :s_name, :passwd, current_timestamp())";

$stmt = $dbh->prepare($sql);

$params = [
    ':s_no' => $no,
    ':s_name' => $name,
    ':passwd' => $pass
];

$stmt->execute($params);
```

### Quy trình

```text
$_POST
   ↓
Biến PHP
   ↓
SQL
   ↓
prepare()
   ↓
execute()
   ↓
MySQL
```

---

# 14. 5月27日 — Password Hash

## `password_hash()`

Không nên lưu password trực tiếp:

```php
$pass = $_POST['password'];
```

Ví dụ không nên:

```text
password = 123456
```

Thay vào đó:

```php
$hashedPassword = password_hash(
    $pass,
    PASSWORD_DEFAULT
);
```

Sau đó lưu:

```php
$params = [
    ':s_no' => $no,
    ':s_name' => $name,
    ':passwd' => $hashedPassword
];
```

### Hoàn chỉnh

```php
<?php

require_once 'dbconnect.php';

$no = $_POST['std_no'];
$name = $_POST['std_name'];
$pass = $_POST['password'];

$hashedPassword = password_hash(
    $pass,
    PASSWORD_DEFAULT
);

$sql = "INSERT INTO address
        (std_no, std_name, password, date)
        VALUES
        (:s_no, :s_name, :passwd, current_timestamp())";

$stmt = $dbh->prepare($sql);

$params = [
    ':s_no' => $no,
    ':s_name' => $name,
    ':passwd' => $hashedPassword
];

$stmt->execute($params);

?>
```

---

# 15. Course Registration — 履修科目登録

## `courseTaken_input.php`

```html
<!DOCTYPE html>
<html lang="ja">

<body>

<h2>履修科目登録</h2>

<form method="post" action="coursesTaken_input_1.php">

    <p>
        学籍番号 (Student No): <br>
        <input type="number" name="std_no" required>
    </p>

    <p>
        パスワード (Password): <br>
        <input type="password" name="password" required>
    </p>

    <button type="submit">ログオン</button>

</form>

</body>
</html>
```

---

# 16. Kiểm tra Student No + Password

```php
$no = $_POST['std_no'];
$pass = $_POST['password'];

$sql = "SELECT * FROM address WHERE std_no = " . $no;

$stmt = $dbh->query($sql);

foreach ($stmt as $row) {

    $name = $row['std_name'];
    $dbPassword = $row['password'];

}
```

---

# 17. `password_verify()`

Kiểm tra password người dùng nhập với password đã hash trong database:

```php
if (password_verify($pass, $dbPassword)) {

    echo "<h2>学籍番号：" . $no .
         "　氏名：" . $name . "</h2>";

}
```

### Nhớ

```text
Đăng ký
password
   ↓
password_hash()
   ↓
Database
```

Khi đăng nhập:

```text
Password người dùng nhập
   ↓
password_verify()
   ↓
Password đã hash trong Database
```

---

# 18. Lấy danh sách Subject

```php
$subject_list = array();
$count = 0;

$sql = "SELECT * FROM subject";

$stmt = $dbh->query($sql);

foreach ($stmt as $row) {

    $subject_list[$count] = $row['subject_name'];
    $count++;

}
```

---

# 19. Hiển thị Checkbox

```php
for ($i = 0; $i < $count; $i++) {

    echo '<label>';

    echo '<input type="checkbox"
                 name="subjects[]"
                 value="' . $subject_list[$i] . '">';

    echo $subject_list[$i];

    echo '</label><br>';
}
```

### Quan trọng

```html
name="subjects[]"
```

`[]` có nghĩa là gửi **nhiều giá trị** dưới dạng array.

Ví dụ:

```text
subjects[] =
    PHP
    SQL
    JavaScript
```

---

# 6月3日 — Search User

# 20. `address_select.php`

```html
<!DOCTYPE html>
<html lang="ja">

<body>

<h2>登録済みユーザ検索</h2>

<form method="post" action="address_select_1.php">

    <p>
        学籍番号 (Student No): <br>

        <input
            type="number"
            id="userId"
            name="std_no"
        >

        <p id="result"></p>
    </p>

    <p>
        氏名 (Name): <br>

        <input
            type="text"
            name="std_name"
        >
    </p>

    <button type="submit">検索</button>

</form>

</body>
</html>
```

---

# 21. Nhận dữ liệu tìm kiếm

```php
if (!empty($_POST['std_no'])) {
    $no = $_POST['std_no'];
} else {
    $no = 0;
}

if (!empty($_POST['std_name'])) {
    $name = $_POST['std_name'];
} else {
    $name = 0;
}
```

---

# 22. SELECT tất cả User

```php
if (!$no && !$name) {

    $count = 1;

    $sql = "SELECT * FROM address";

    $stmt = $dbh->query($sql);

    foreach ($stmt as $row) {

        echo $count . ") "
           . $row['std_no']
           . " "
           . $row['std_name']
           . "<br>";

        $count++;
    }
}
```

---

# 23. Tìm kiếm bằng `LIKE`

```sql
SELECT *
FROM address
WHERE std_no LIKE '%223%';
```

### Ý nghĩa

```text
%223%
 ↓
Có chứa "223" ở bất kỳ vị trí nào
```

Ví dụ:

```text
2231513
1223151
9992239
```

đều có thể tìm thấy.

---

# 24. Search Student No bằng PHP

```php
if ($no) {

    $count = 1;

    $sql = "SELECT * FROM address
            WHERE std_no LIKE '%" . $no . "%'";

    $stmt = $dbh->query($sql);

    foreach ($stmt as $row) {

        echo $count . ") "
           . $row['std_no']
           . " "
           . $row['std_name']
           . "<br>";

        $count++;
    }
}
```

---

# 25. Search Name

```php
if ($name) {

    if ($count > 1) {
        echo "<br><br>";
    }

    $count = 1;

    $sql = "SELECT * FROM address
            WHERE std_name LIKE '%" . $name . "%'";

    $stmt = $dbh->query($sql);

    foreach ($stmt as $row) {

        echo $count . ") "
           . $row['std_no']
           . " "
           . $row['std_name']
           . "<br>";

        $count++;
    }
}
```

---

# 6月17日 — JavaScript + PHP

# 26. Kiểm tra Student ID bằng JavaScript

File:

```text
javascript_sample.html
```

```html
<input
    type="number"
    id="userId"
    name="std_no"
    required
>

<button onclick="checkId()">確認</button>

<p id="result"></p>
```

---

# 27. JavaScript `fetch()`

```javascript
async function checkId() {

    const id =
        document.getElementById("userId").value;

    const response = await fetch(
        "userId_check.php",
        {
            method: "POST",

            headers: {
                "Content-Type":
                    "application/x-www-form-urlencoded"
            },

            body:
                "id=" +
                encodeURIComponent(id)
        }
    );

    const result = await response.text();

    document
        .getElementById("result")
        .textContent = result;
}
```

---

# 28. Quy trình JavaScript → PHP

```text
HTML
 ↓
JavaScript
 ↓
fetch()
 ↓
POST
 ↓
userId_check.php
 ↓
MySQL
 ↓
Kết quả
 ↓
JavaScript
 ↓
HTML
```

---

# 29. `userId_check.php`

```php
<?php

$host = "localhost";
$dbname = "webpg2";
$user = "root";
$pass = "";

$conn = new PDO(
    "mysql:host=$host;dbname=$dbname;charset=utf8",
    $user,
    $pass
);

$id = $_POST["id"];

$sql = "SELECT COUNT(*) FROM address
        WHERE std_no = ?";

$stmt = $conn->prepare($sql);

$stmt->execute([$id]);

$count = $stmt->fetchColumn();

if ($count > 0) {

    echo "既に登録されている学籍番号です！";

} else {

    echo "この学籍番号は登録できます。";

}

?>
```

---

# 30. `fetchColumn()`

```php
$count = $stmt->fetchColumn();
```

Dùng để lấy **một giá trị** từ kết quả SQL.

Ví dụ:

```sql
SELECT COUNT(*)
FROM address
WHERE std_no = ?
```

Kết quả:

```text
0
```

hoặc:

```text
1
```

---

# 6月24日 — Subject Update

# 31. `subject_update.php`

```html
<!DOCTYPE html>
<html lang="ja">

<body>

<h2>登録済み科目修正</h2>

<form method="post" action="subject_update_1.php">

    <p>
        科目名: <br>

        <input
            type="text"
            name="subject_name"
            required
        >
    </p>

    <button type="submit">修正</button>

</form>

</body>
</html>
```

---

# 32. Tìm Subject cần sửa

File:

```text
subject_update_1.php
```

```php
<?php

require_once 'dbconnect.php';

if (!empty($_POST['subject_name'])) {

    echo "<h1>登録科目修正</h1>";

    $name = $_POST['subject_name'];

    $sql = "SELECT * FROM subject
            WHERE subject_name LIKE '%" . $name . "%'";

    $stmt = $dbh->query($sql);

    foreach ($stmt as $row) {

        $subject_name = $row['subject_name'];

        echo '<form
                action="subject_update_2.php"
                method="post">';

        echo $subject_name;

        echo '<input
                type="text"
                name="update_name">';

        echo '<input
                type="hidden"
                name="subject_name"
                value="' . $subject_name . '">';

        echo '<input
                type="submit"
                value="修正">';

        echo '</form>';
    }
}

?>
```

---

# 33. UPDATE Subject

File:

```text
subject_update_2.php
```

```php
<?php

require_once 'dbconnect.php';

if (!empty($_POST['update_name'])) {

    $update_name = $_POST['update_name'];

    $subject_name = $_POST['subject_name'];

    echo "修正前:"
        . $subject_name
        . " ---修正後: "
        . $update_name;

    $sql1 = "UPDATE subject
             SET subject_name = :upd_name
             WHERE subject_name = :sub_name";

    $stmt1 = $dbh->prepare($sql1);

    $params = [
        ':upd_name' => $update_name,
        ':sub_name' => $subject_name
    ];

    $stmt1->execute($params);
}

?>
```

---

# 34. UPDATE Flow

```text
subject_update.php
        ↓
Nhập tên môn học
        ↓
subject_update_1.php
        ↓
SELECT + LIKE
        ↓
Hiển thị môn học
        ↓
Nhập tên mới
        ↓
subject_update_2.php
        ↓
UPDATE
        ↓
Database
```

---

# 7月24日 — Teacher & Teacher Subject

# 35. Teacher Table

### Yêu cầu

> teacherのテーブルおよび登録プログラムを作成する。

Tạo bảng `teacher` và chương trình đăng ký giáo viên.

### Table

```text
teacher
```

### Columns

```text
id
user_id
name
password
```

---

# 36. Teacher Subject Table

### Yêu cầu

> 教員の担当科目テーブルおよび登録プログラムを作成する。

Tạo bảng môn học mà giáo viên phụ trách và chương trình đăng ký.

### Table

```text
tea_subject
```

### Columns

```text
id
teacher_id
subject_id
```

---

# 37. Quan hệ giữa các bảng

```text
teacher
   │
   │ teacher_id
   ↓
tea_subject
   │
   │ subject_id
   ↓
subject
```

Ý nghĩa:

```text
1 Teacher
   ↓
có thể phụ trách
   ↓
nhiều Subject
```

---

# 38. Tổng hợp kiến thức cần nhớ

## PHP

```text
$_POST
$_SESSION
require()
require_once()
password_hash()
password_verify()
foreach
if
header()
echo
```

---

## PDO

```text
new PDO()
prepare()
execute()
query()
fetchColumn()
```

---

## SQL

```text
CREATE TABLE
INSERT
SELECT
WHERE
LIKE
UPDATE
ALTER TABLE
ORDER BY
COUNT()
FOREIGN KEY
```

---

## HTML Form

```html
<form method="post" action="...">

<input>
<select>
<button>
```

---

## JavaScript

```javascript
async
await
fetch()
document.getElementById()
textContent
encodeURIComponent()
```

---

# 39. CRUD

Một trong những phần quan trọng nhất của Database:

```text
C = CREATE
    ↓
INSERT

R = READ
    ↓
SELECT

U = UPDATE
    ↓
UPDATE

D = DELETE
    ↓
DELETE
```

### Ví dụ

```sql
-- CREATE
INSERT INTO subject (subject_name)
VALUES ('PHP');

-- READ
SELECT * FROM subject;

-- UPDATE
UPDATE subject
SET subject_name = 'PHP/MySQL'
WHERE id = 1;

-- DELETE
DELETE FROM subject
WHERE id = 1;
```

---

# 40. Luồng Web cơ bản

```text
                Browser
                   │
                   ↓
                HTML Form
                   │
                   ↓
                PHP
                   │
                   ↓
                PDO
                   │
                   ↓
                MySQL
                   │
                   ↓
                Database
                   │
                   ↓
                PHP
                   │
                   ↓
                HTML
                   │
                   ↓
                Browser
```

---

# 41. Các file quan trọng

```text
dbconnect.php
        ↓
Kết nối Database

address_input.php
        ↓
Form đăng ký User

address_input_1.php
        ↓
INSERT User

courseTaken_input.php
        ↓
Form đăng nhập + đăng ký môn

coursesTaken_input_1.php
        ↓
Kiểm tra Password + hiển thị Subject

address_select.php
        ↓
Form Search

address_select_1.php
        ↓
SELECT + LIKE

javascript_sample.html
        ↓
JavaScript + fetch()

userId_check.php
        ↓
Kiểm tra Student ID

subject_update.php
        ↓
Form Update Subject

subject_update_1.php
        ↓
Tìm Subject

subject_update_2.php
        ↓
UPDATE Subject

teacher_register.php
        ↓
Đăng ký Teacher

tea_subject_register.php
        ↓
Đăng ký Teacher phụ trách Subject
```

---

# 42. Sơ đồ kiến thức toàn bộ

```text
                    PHP / MySQL
                         │
        ┌────────────────┼────────────────┐
        │                │                │
       PHP              SQL          JavaScript
        │                │                │
   ┌────┼────┐      ┌────┼────┐          │
   │    │    │      │    │    │          │
 POST SESSION PDO  SELECT INSERT UPDATE  fetch()
   │    │    │      │    │    │          │
   │    │    │      │    │    │          │
   └────┴────┴──────┴────┴────┴──────────┘
                    │
                    ↓
                  MySQL
                    │
          ┌─────────┼─────────┐
          │         │         │
       address   subject   teacher
                              │
                              ↓
                        tea_subject
```

---

# 43. Những phần đặc biệt cần nhớ khi ôn thi

## ① Kết nối Database

```php
$dbh = new PDO(
    "mysql:host=localhost;dbname=webpg2;charset=utf8",
    "root",
    ""
);
```

## ② INSERT

```php
$sql = "INSERT INTO address
        (std_no, std_name, password)
        VALUES (:no, :name, :password)";

$stmt = $dbh->prepare($sql);

$stmt->execute([
    ':no' => $no,
    ':name' => $name,
    ':password' => $password
]);
```

## ③ SELECT

```php
$sql = "SELECT * FROM address";

$stmt = $dbh->query($sql);

foreach ($stmt as $row) {

    echo $row['std_name'];

}
```

## ④ UPDATE

```php
$sql = "UPDATE subject
        SET subject_name = :new_name
        WHERE subject_name = :old_name";

$stmt = $dbh->prepare($sql);

$stmt->execute([
    ':new_name' => $new_name,
    ':old_name' => $old_name
]);
```

## ⑤ Password Hash

```php
$hash = password_hash(
    $password,
    PASSWORD_DEFAULT
);
```

## ⑥ Password Verify

```php
if (password_verify($password, $hash)) {

    // Login OK

}
```

## ⑦ Session

```php
session_start();

$_SESSION['user_id'] = $user_id;
```

Lấy lại:

```php
session_start();

$user_id = $_SESSION['user_id'];
```

## ⑧ LIKE

```sql
SELECT *
FROM subject
WHERE subject_name LIKE '%PHP%';
```

## ⑨ Foreign Key

```sql
FOREIGN KEY (teacher_id)
REFERENCES teacher(id);
```

---

# 44. Tóm tắt cực ngắn

```text
HTML
 ↓
Form
 ↓
$_POST
 ↓
PHP
 ↓
PDO
 ↓
prepare() / query()
 ↓
execute()
 ↓
MySQL
 ↓
SELECT / INSERT / UPDATE
 ↓
PHP
 ↓
HTML
```

### Authentication

```text
Password
 ↓
password_hash()
 ↓
Database
```

```text
Password nhập lại
 ↓
password_verify()
 ↓
OK / NG
```

### Session

```text
Login
 ↓
$_SESSION
 ↓
user_id
 ↓
SELECT User
 ↓
Hiển thị / Update
```
