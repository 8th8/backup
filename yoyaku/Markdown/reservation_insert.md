1. Sơ đồ hoạt động tổng thể
                        `reservation.html`
                    Khách hàng nhập thông tin đặt bàn
            Ngày, giờ, tên, điện thoại, số người, ghi chú
                                |
                        `reservation_insert.php`
                            Backend xử lý
                        Nhận dữ liệu bằng POST
                                |
                        `Kiểm tra dữ liệu đầu vào`
            Ngày tháng, thời gian, số điện thoại, số người, trường bắt buộc
                                |
                        `Tạo mã đặt bàn + INSERT SQL`
                    Lưu dữ liệu vào bảng reservations
        ---------------------------------------------------------            
        |                                                       |
    `Thành công`                                          `Thất bại`
     header()                                              catch
     Chuyển trang hoàn tất                                 Xử lý lỗi database 

                                |
                    `reservation_complete.php`
                     Hiển thị kết quả đặt bàn

=========================================================================
2. Giải thích 11 bước trong code
Bước 1. Kết nối Database và thiết lập múi giờ
`
require_once __DIR__ . "/config/dbconnect.php";

date_default_timezone_set('Asia/Tokyo');
`

Code	                            Ý nghĩa
require_once	            Gọi file PHP khác và chỉ gọi một lần
__DIR__	                    Đường dẫn thư mục hiện tại của file PHP
dbconnect.php	            File chứa thông tin kết nối database
date_default_timezone_set()	Thiết lập múi giờ
Asia/Tokyo	                Múi giờ Nhật Bản

Mục đích: kết nối đến database yoyaku và sử dụng giờ Nhật Bản khi xử lý ngày tháng.
==============================================================================
Bước 2. Chỉ cho phép gửi dữ liệu bằng POST
`
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("不正なアクセスです。");
}
`

Code	        Ý nghĩa
$_SERVER	    Mảng chứa thông tin về máy chủ và yêu cầu HTTP
REQUEST_METHOD	Phương thức gửi yêu cầu
!==	            So sánh nghiêm ngặt và khác
POST	        Phương thức gửi dữ liệu
405	            Method Not Allowed
exit()	        Dừng chương trình

Mục đích: không cho phép người dùng truy cập trực tiếp file xử lý bằng GET.
============================================================================
Bước 3. Nhận dữ liệu từ Form

$reservation_date = trim($_POST["reservation_date"] ?? "");
$reservation_time = trim($_POST["reservation_time"] ?? "");
$customer_phone = trim($_POST["customer_phone"] ?? "");
$customer_name = trim($_POST["customer_name"] ?? "");
$note = trim($_POST["note"] ?? "");


Ở đây, PHP lấy dữ liệu từ form HTML và lưu vào các biến.

Ví dụ:
Form HTML	           Biến PHP
reservation_date	$reservation_date
reservation_time	$reservation_time
customer_phone	    $customer_phone
customer_name	    $customer_name
note	            $note

trim() loại bỏ khoảng trắng ở đầu và cuối chuỗi.
?? "" cung cấp giá trị mặc định là chuỗi rỗng nếu dữ liệu không tồn tại hoặc bằng null.
Riêng số người:
$party_size = filter_input(
    INPUT_POST,
    "party_size",
    FILTER_VALIDATE_INT
);

PHP lấy dữ liệu party_size bằng POST và kiểm tra xem có phải số nguyên hợp lệ không.
======================================================================================
Bước 4. Kiểm tra số người
`
if (
    $party_size === false ||
    $party_size === null ||
    $party_size < 1 ||
    $party_size > 10
) {
    exit("人数を正しく選択してください。");
}
`

Điều kiện kiểm tra:
- === false: dữ liệu không phải số nguyên hợp lệ.
- === null: không nhận được dữ liệu.
- < 1: số người nhỏ hơn 1.
- > 10: số người lớn hơn 10.
Chỉ chấp nhận từ 1 đến 10 người.
============================================================================================
Bước 5. Kiểm tra các trường bắt buộc
`
$missing = [];

if ($reservation_date === "")
    $missing[] = "ご来店日 (reservation_date)";

if ($reservation_time === "")
    $missing[] = "ご来店時間 (reservation_time)";

if ($customer_phone === "")
    $missing[] = "電話番号 (customer_phone)";

if ($customer_name === "")
    $missing[] = "お名前 (customer_name)";

if ($missing) {
    exit("必須項目を入力してください。未入力: "
        . implode("、", $missing));
}
`
Mục đích: kiểm tra xem khách hàng đã nhập đủ thông tin bắt buộc chưa.

Các kiến thức sử dụng:
Code	        Ý nghĩa
$missing = []	Tạo mảng rỗng
$missing[]	    Thêm phần tử vào cuối mảng
=== ""	        Kiểm tra chuỗi rỗng
if ($missing)	Kiểm tra mảng có phần tử hay không
implode()	    Nối các phần tử mảng thành chuỗi

Ví dụ khách chưa nhập số điện thoại và tên:
必須項目を入力してください。
未入力: 電話番号 (customer_phone)、お名前 (customer_name)

============================================================================
Bước 6. Kiểm tra định dạng ngày tháng

$date = DateTime::createFromFormat(
    "!Y-m-d",
    $reservation_date
);


Chuyển chuỗi ngày tháng thành đối tượng DateTime.
- DateTime: lớp xử lý ngày tháng.
- createFromFormat(): chuyển chuỗi theo định dạng quy định.
- !Y-m-d: định dạng năm-tháng-ngày, đồng thời đặt các phần thời gian không được chỉ định về giá trị mặc định.
Tiếp theo:
if (
    !$date ||
    $date->format("Y-m-d") !== $reservation_date ||
    $date < new DateTime("today")
) {
    exit("ご来店日を正しく入力してください。");
}

Có 3 điều kiện:
1. !$date: không tạo được ngày hợp lệ.
2. $date->format("Y-m-d") !== $reservation_date: ngày sau khi chuyển đổi không khớp với chuỗi ban đầu.
3. $date < new DateTime("today"): ngày đặt bàn nằm trong quá khứ.
Ví dụ:
Ngày nhập	Kết quả
2026-11-05	Hợp lệ nếu hôm nay là 2026-11-02
2026-10-30	Không hợp lệ
2026-02-30	Không hợp lệ
abc	Không hợp lệ

=========================================================================================
Bước 7. Kiểm tra giờ và số điện thoại

A. Kiểm tra thời gian
if (
    !preg_match(
        '/^(?:[01]\d|2[0-3]):[0-5]\d$/',
        $reservation_time
    )
) {
    exit("ご来店時間を正しく入力してください。");
}


Kiểm tra định dạng giờ 24 tiếng HH:MM.

Ví dụ:
Giờ	Kết quả
09:00	Hợp lệ
18:30	Hợp lệ
23:59	Hợp lệ
25:00	Không hợp lệ
18:70	Không hợp lệ
9:00	Không hợp lệ

B. Kiểm tra số điện thoại
if (!preg_match('/^[0-9+\-\s()]{9,20}$/', $customer_phone)) {
    exit("電話番号を正しく入力してください。");
}

Kiểm tra:
- 0-9: chữ số.
- +: dấu cộng.
- -: dấu gạch ngang.
- \s: khoảng trắng.
- (): dấu ngoặc đơn.
- {9,20}: độ dài từ 9 đến 20 ký tự.
Lưu ý: đây là kiểm tra định dạng cơ bản, chưa xác nhận số điện thoại thực sự tồn tại hoặc thuộc nhà mạng Nhật Bản.
==========================================================================================
Bước 8. Tạo mã đặt bàn

$reservation_code = bin2hex(random_bytes(3));

Hai hàm kết hợp:
- random_bytes(3): tạo 3 byte ngẫu nhiên an toàn.
- bin2hex(): chuyển thành chuỗi hệ thập lục phân.
Kết quả có 6 ký tự hex, ví dụ:
a1b2c3

===========================================================================================
Bước 9. Viết câu lệnh SQL INSERT

$sql = "
    INSERT INTO reservations (
        reservation_code,
        customer_name,
        customer_phone,
        reservation_date,
        reservation_time,
        party_size,
        note
    ) VALUES (
        :reservation_code,
        :customer_name,
        :customer_phone,
        :reservation_date,
        :reservation_time,
        :party_size,
        :note
    )
";


Mục đích: thêm một dòng dữ liệu mới vào bảng reservations.
SQL	Ý nghĩa
INSERT INTO	Thêm dữ liệu
reservations	Tên bảng
VALUES	Giá trị cần thêm
:reservation_code	Tham số giữ chỗ
:customer_name	Tham số giữ chỗ
:customer_phone	Tham số giữ chỗ

===================================================================================================
Bước 10. Chuẩn bị và thực thi SQL

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ":reservation_code" => $reservation_code,
    ":customer_name" => $customer_name,
    ":customer_phone" => $customer_phone,
    ":reservation_date" => $reservation_date,
    ":reservation_time" => $reservation_time,
    ":party_size" => $party_size,
    ":note" => $note !== "" ? $note : null
]);


Code	        Ý nghĩa
$pdo	        Đối tượng kết nối database
prepare()	    Chuẩn bị câu lệnh SQL
$stmt	        Đối tượng câu lệnh đã chuẩn bị
execute()	    Truyền dữ liệu và thực thi SQL
=>	            Liên kết khóa và giá trị trong mảng

Đặc biệt, dòng này:
":note" => $note !== "" ? $note : null


Sử dụng toán tử ba ngôi (Ternary Operator).

Có nghĩa là:
- Nếu $note không rỗng → lưu nội dung ghi chú.
- Nếu $note rỗng → lưu NULL vào database.
=====================================================================================================
Bước 11. Chuyển sang trang hoàn tất

header(
    "Location: reservation_complete.php?code="
    . urlencode($reservation_code)
);
exit;

Mục đích: chuyển khách hàng sang trang hoàn tất và gửi mã đặt bàn qua URL.

Ví dụ:
reservation_complete.php?code=a1b2c3

Trong trang tiếp theo, nhận mã bằng:
$reservation_code = $_GET["code"] ?? "";
==================================================================================================
Bước 12. Xử lý lỗi bằng try - catch

Trong code của bạn, phần này nằm cuối chương trình:

} catch (PDOException $e) {

    error_log($e->getMessage());

    http_response_code(500);
    exit("予約の登録に失敗しました。");
}

Code	                    Ý nghĩa
try	                    Thử thực hiện code
catch	                Bắt lỗi
PDOException	        Lỗi liên quan đến PDO
$e	                    Đối tượng chứa thông tin lỗi
error_log()	            Ghi lỗi vào log
getMessage()	        Lấy nội dung lỗi
http_response_code(500)	Trả mã lỗi máy chủ
exit()	                Dừng chương trình

Nếu câu lệnh SQL bị lỗi, chương trình sẽ chuyển sang catch để xử lý.

4. Bản đồ kiến thức PHP đã sử dụng
Đây là bảng tổng hợp để bạn ôn tập khi làm dự án.
--------------------------------------------------------------------
| Nhóm kiến thức   | Các hàm / cú pháp                              |
|------------------|------------------------------------------------|
| Nhận dữ liệu     | $_POST, $_SERVER, filter_input()               |
| Xử lý chuỗi      | trim(), implode(), urlencode()                 |
| Điều kiện        | if, ||, ===, !==, ??                           |
| Mảng             | [], =>                                         |
| Kiểm tra dữ liệu | preg_match(), FILTER_VALIDATE_INT              |
| Ngày tháng       | DateTime, createFromFormat(), format()         |
| Tạo mã           | random_bytes(), bin2hex()                      |
| Database         | $pdo, prepare(), $stmt, execute(), INSERT INTO |
| Xử lý lỗi        | try, catch, PDOException, error_log()          |
| HTTP             | header(), http_response_code(), exit()         |
---------------------------------------------------------------------

5. Bạn cần hiểu điều gì sau khi học file này?
Có 3 kiến thức lớn:
01
Input Validation – 入力チェック

Kiểm tra dữ liệu đầu vào trước khi lưu database.

Ví dụ: ngày hợp lệ, giờ hợp lệ, số điện thoại đúng định dạng, số người từ 1–10.

02
Database Processing – データベース処理

Kết nối database, chuẩn bị câu lệnh SQL và lưu thông tin đặt bàn.

Ví dụ: prepare(), execute(), INSERT INTO.

03
Error Handling – エラー処理

Xử lý lỗi để chương trình không hiển thị thông tin kỹ thuật nhạy cảm cho khách hàng.

Ví dụ: try, catch, PDOException.

Lời khuyên: Đừng chỉ học thuộc từng hàm. Hãy hiểu tại sao phải dùng chúng theo đúng thứ tự:
1. Nhận dữ liệu.
2. Kiểm tra dữ liệu.
3. Tạo mã đặt bàn.
4. Lưu vào database.
5. Chuyển trang nếu thành công.
6. Xử lý lỗi nếu thất bại.
Khi hiểu được 6 bước này, bạn đã nắm được nền tảng của một chức năng Backend PHP thực tế.