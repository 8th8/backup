# Database & PHP Notes

## 1. Từ vựng

| Từ            | Ý nghĩa                |
| ------------- | ---------------------- |
| `teacher`     | Giảng viên             |
| `tea_subject` | Môn học của giảng viên |
| `subject`     | Môn học                |
| `course`      | Khóa học               |
| `register`    | Đăng ký                |
| `ファムティカンリン`   | 2242517                |

---

# 2. Teacher Table

### Yêu cầu

> teacherのテーブルおよび登録プログラムを作成する。
> Tạo bảng `teacher` và chương trình đăng ký giảng viên.

### Table name

```text
teacher
```

### Các cột

| Column     | Kiểu dữ liệu | Ý nghĩa            |
| ---------- | ------------ | ------------------ |
| `id`       | INT          | ID tự tăng         |
| `user_id`  | VARCHAR(50)  | ID người dùng      |
| `name`     | VARCHAR(100) | Tên giảng viên     |
| `password` | VARCHAR(255) | Mật khẩu đã mã hóa |

### SQL tạo bảng

```sql
CREATE TABLE teacher (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL
);
```

> **Lưu ý:** Trong phần code ban đầu có chỗ viết `teacher_id`, nhưng form và câu lệnh `INSERT` lại sử dụng `user_id`. Hai tên này phải thống nhất. Ở đây sử dụng `user_id`.

---

## 3. Form đăng ký Teacher

```html
<form action="teacher_register.php" method="post">

    <label>ユーザーID</label>
    <input type="text" name="user_id" required>

    <label>氏名</label>
    <input type="text" name="name" required>

    <label>パスワード</label>
    <input type="password" name="password" required>

    <button type="submit">登録</button>

</form>
```

---

## 4. PHP đăng ký Teacher

```php
<?php

require('./dbconnect.php');

$user_id = $_POST['user_id'];
$name = $_POST['name'];
$password = $_POST['password'];

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO teacher (user_id, name, password)
        VALUES (:user_id, :name, :password)";

$stmt = $dbh->prepare($sql);

$params = [
    ':user_id' => $user_id,
    ':name' => $name,
    ':password' => $hashedPassword
];

$stmt->execute($params);

echo "教員登録が完了しました。";

?>
```

### Giải thích

```php
password_hash($password, PASSWORD_DEFAULT);
```

→ Mã hóa password trước khi lưu vào database.

```php
$dbh->prepare($sql);
```

→ Chuẩn bị câu SQL bằng PDO.

```php
$stmt->execute($params);
```

→ Thực thi SQL và truyền dữ liệu vào các `:parameter`.

---

# 5. Teacher Subject Table

### Yêu cầu

> 教員の担当科目テーブルおよび登録プログラムを作成する。
> Tạo bảng môn học mà giảng viên phụ trách và chương trình đăng ký.

### Table name

```text
tea_subject
```

### Các cột

| Column       | Kiểu dữ liệu | Ý nghĩa           |
| ------------ | ------------ | ----------------- |
| `id`         | INT          | ID tự tăng        |
| `teacher_id` | INT          | ID của giảng viên |
| `subject_id` | INT          | ID của môn học    |

---

## 6. SQL tạo bảng `tea_subject`

```sql
CREATE TABLE tea_subject (
    id INT AUTO_INCREMENT PRIMARY KEY,
    teacher_id INT NOT NULL,
    subject_id INT NOT NULL,

    FOREIGN KEY (teacher_id)
        REFERENCES teacher(id),

    FOREIGN KEY (subject_id)
        REFERENCES courses(id)
);
```

### Quan hệ

```text
teacher
   │
   │ teacher.id
   ↓
tea_subject
   │
   │ subject_id
   ↓
courses
```

`tea_subject` là bảng trung gian dùng để liên kết:

```text
Giảng viên ←→ Môn học
```

---

# 7. Form đăng ký Teacher Subject

```html
<form action="tea_subject_register.php" method="post">

    <label>教員ID</label>
    <input type="number" name="teacher_id" required>

    <label>科目ID</label>
    <input type="text" name="subject_id" required>

    <button type="submit">登録</button>

</form>
```

---

# 8. PHP đăng ký Teacher Subject

```php
<?php

require('./dbconnect.php');

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

?>
```

---

# 9. Tạo bảng User

```sql
CREATE TABLE user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_no VARCHAR(20),
    name VARCHAR(100),
    password VARCHAR(50)
);
```

> **Lưu ý:** Nếu sử dụng MySQL, `user` có thể gây nhầm với tên hệ thống/quyền của MySQL. Trong project thực tế nên cân nhắc tên như `users` hoặc `student`.

---

# 10. Thêm dữ liệu vào bảng

### Cú pháp

```sql
INSERT INTO user (student_no, name, password)
VALUES ('2231513', '鈴木 潤', '2231513');
```

### Ý nghĩa

```text
student_no → 2231513
name       → 鈴木 潤
password   → 2231513
```

---

# 11. Thêm một cột vào bảng

Ví dụ thêm cột `gender`:

```sql
ALTER TABLE user
ADD COLUMN gender VARCHAR(10);
```

---

# 12. Cập nhật giới tính ngẫu nhiên

```sql
UPDATE user
SET gender =
CASE
    WHEN RAND() < 0.5 THEN '男'
    ELSE '女'
END;
```

### Ý nghĩa

```text
RAND() < 0.5
       ↓
    50% 男
    50% 女
```

---

# 13. SELECT - Lấy dữ liệu từ bảng

```sql
SELECT *
FROM address
ORDER BY student_no;
```

### Ý nghĩa

```text
SELECT *
    ↓
Lấy tất cả các cột

FROM address
    ↓
Từ bảng address

ORDER BY student_no
    ↓
Sắp xếp theo student_no
```

---

# 14. SELECT bằng PHP với `prepare()`

```php
$sql = "SELECT * FROM address
        WHERE student_no = :student_no";

$stmt = $dbh->prepare($sql);

$stmt->execute([
    ":student_no" => $student_no
]);
```

### Quy trình

```text
SQL
 ↓
prepare()
 ↓
execute()
 ↓
Database
 ↓
Kết quả
```

---

# 15. Kết nối Database bằng PDO

File:

```text
dbconnect.php
```

Code:

```php
<?php

$host = "localhost";
$dbname = "webpg2";
$user = "root";
$pass = "";

$dbh = new PDO(
    "mysql:host=$host;dbname=$dbname;charset=utf8",
    $user,
    $pass
);

?>
```

### Giải thích

| Biến      | Ý nghĩa                 |
| --------- | ----------------------- |
| `$host`   | Địa chỉ database server |
| `$dbname` | Tên database            |
| `$user`   | Username MySQL          |
| `$pass`   | Password MySQL          |
| `$dbh`    | Đối tượng kết nối PDO   |

---

# 16. INSERT bằng PHP

```php
$sql = "INSERT INTO user (student_no, name, password)
        VALUES (:student_no, :name, :password)";
```

### Kết hợp với `prepare()` và `execute()`

```php
$sql = "INSERT INTO user (student_no, name, password)
        VALUES (:student_no, :name, :password)";

$stmt = $dbh->prepare($sql);

$stmt->execute([
    ':student_no' => $student_no,
    ':name' => $name,
    ':password' => $password
]);
```

---

# 17. Các câu SQL quan trọng cần nhớ

## CREATE TABLE

Tạo bảng:

```sql
CREATE TABLE table_name (
    id INT AUTO_INCREMENT PRIMARY KEY
);
```

## INSERT

Thêm dữ liệu:

```sql
INSERT INTO table_name (column1, column2)
VALUES ('value1', 'value2');
```

## SELECT

Lấy dữ liệu:

```sql
SELECT *
FROM table_name;
```

## WHERE

Tìm dữ liệu theo điều kiện:

```sql
SELECT *
FROM table_name
WHERE student_no = '2231513';
```

## ORDER BY

Sắp xếp:

```sql
SELECT *
FROM table_name
ORDER BY student_no;
```

## UPDATE

Cập nhật:

```sql
UPDATE table_name
SET name = 'Nguyễn Văn A'
WHERE id = 1;
```

## ALTER TABLE

Thay đổi cấu trúc bảng:

```sql
ALTER TABLE table_name
ADD COLUMN gender VARCHAR(10);
```

## FOREIGN KEY

Tạo khóa ngoại:

```sql
FOREIGN KEY (teacher_id)
REFERENCES teacher(id);
```

---

# 18. Các từ khóa PHP / PDO cần nhớ

| Code              | Ý nghĩa                   |
| ----------------- | ------------------------- |
| `require()`       | Gọi file PHP khác         |
| `new PDO()`       | Tạo kết nối database      |
| `prepare()`       | Chuẩn bị câu SQL          |
| `execute()`       | Thực thi SQL              |
| `password_hash()` | Mã hóa password           |
| `$_POST`          | Nhận dữ liệu từ form POST |
| `INSERT`          | Thêm dữ liệu              |
| `SELECT`          | Lấy dữ liệu               |
| `UPDATE`          | Cập nhật dữ liệu          |
| `DELETE`          | Xóa dữ liệu               |

---

# 19. Cấu trúc project gợi ý

```text
project/
│
├── dbconnect.php
│
├── teacher_register.php
├── tea_subject_register.php
│
├── index.php
│
├── css/
│   └── style.css
│
└── notes/
    └── database-notes.md
```

---

# 20. Luồng đăng ký Teacher

```text
HTML Form
    ↓
teacher_register.php
    ↓
$_POST
    ↓
password_hash()
    ↓
prepare()
    ↓
execute()
    ↓
teacher table
```

# 21. Luồng đăng ký môn học của Teacher

```text
HTML Form
    ↓
tea_subject_register.php
    ↓
$_POST
    ↓
prepare()
    ↓
execute()
    ↓
tea_subject table
```
