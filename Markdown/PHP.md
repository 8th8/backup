## Ghi chú PHP
# -------- Ghi chú nhanh ---------------------------------------------------------
` trim() ` : loại bỏ khoảng trắng thừa (dùng để xóa khoảng trắng ở đầu và cuối chuỗi.) 
`   ||   ` : là toán tử OR (論理和), có nghĩa là HOẶC (Chỉ cần một điều kiện đúng thì toàn bộ biểu thức if sẽ đúng.)
`   ==   ` : So sánh giá trị
`   ===  ` : So sánh cả giá trị và kiểu dữ liệu
` implode() ` : dùng để nối các phần tử trong mảng thành một chuỗi. ( implode("ký_tự_nối", 
$mảng); )
vd :   $missing = [
            "電話番号",
            "お名前"
        ];
        echo implode("、", $missing);

        =>   電話番号、お名前

` DateTime`             lớp xử lý ngày tháng trong PHP.
` createFromFormat() `  chuyển chuỗi theo định dạng chỉ định thành ngày tháng.
` preg_match() `	    Kiểm tra chuỗi có khớp với mẫu quy định không
` random_bytes(8) `	    Tạo 8 byte dữ liệu ngẫu nhiên an toàn
` bin2hex()	`           Chuyển dữ liệu nhị phân thành chuỗi ký tự hệ thập lục phân
` prepare() `           có nghĩa là chuẩn bị câu lệnh SQL để thực thi.
` $stmt ` (statement) = câu lệnh. tên biến thường được lập trình viên đặt để lưu đối tượng câu lệnh SQL đã chuẩn bị.
` execute() `           có nghĩa là thực thi câu lệnh.
`    =>	   `            Gán giá trị cho khóa của mảng
$missing = []	Tạo mảng rỗng
$missing[]	Thêm phần tử vào cuối mảng



# -----  -So sánh $_GET và $_POST  ---------- 
`Tiêu chí`	            `  $_GET  `	                                  ` $_POST  `
`Cách gửi`	            Gửi qua URL	                            Gửi trong HTTP Request Body
`Hiển thị dữ liệu`	    Có, trên URL	                        Không hiển thị trên URL
`Bảo mật`	            Không phù hợp với dữ
                        liệu nhạy cảm	                        Tốt hơn GET về việc không để lộ dữ liệu trên URL
`Dung lượng`	        Bị giới hạn bởi độ dài URL	            Có thể gửi dữ liệu lớn hơn
`Bookmark`	            Có thể lưu URL kèm dữ liệu	            Không thể lưu dữ liệu POST theo cách này
`Mục đích`	          ` Tìm kiếm, lọc dữ liệu  `              ` Gửi form, đăng ký, đặt chỗ `
----------------------------------------------------------------------------------------------

## toán tử Null Coalescing (Null合体演算子).  ( kiểm tra dữ liệu có được gửi không.)

   ` $value = $a ?? $b;  `

Có nghĩa là:
- Nếu $a tồn tại và không phải null → lấy giá trị $a.
- Nếu $a không tồn tại hoặc là null → lấy giá trị $b.

-----------------------------------------------------------------------
##  filter_input( ) --> dùng để `lấy dữ liệu từ nguồn nhập vào` và có thể `kiểm tra ` hoặc `lọc` dữ liệu đó.

` filter_input(nguồn_dữ_liệu, tên_trường, kiểu_kiểm_tra); `

vd:         $party_size = filter_input(
                INPUT_POST,
                "party_size",
                FILTER_VALIDATE_INT
            ); 

` INPUT_POST  `         -> Cho PHP biết cần lấy dữ liệu được gửi bằng phương thức POST từ form HTML. ( nguồn_dữ_liệu )
` party_size `          -> Đây là tên của trường dữ liệu trong HTML. ( tên_trường )
` FILTER_VALIDATE_INT ` -> Đây là bộ lọc kiểm tra xem dữ liệu có phải là số nguyên hợp lệ hay không. (kiểu_kiểm_tra)
                        vd : 2 -> hợp lệ , 2,5 -> không hợp lệ, abc -> không hợp lệ
----------------------------------------------------------------------------------------------------
# try - catch
-try: thử thực hiện đoạn code.
-Nếu thành công: tiếp tục chạy bình thường.
-Nếu xảy ra PDOException: chuyển sang catch.
vd:
--------------------------------
 try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);

} catch (PDOException $e) {                 // Xử lý lỗi
    error_log($e->getMessage());

    http_response_code(500);
    exit("予約の登録に失敗しました。");
}
------------------------
` catch `:        bắt lỗi xảy ra trong quá trình thực thi chương trình.
` PDOException `: loại lỗi liên quan đến PDO và database.
`   $e   ` :      biến chứa thông tin về lỗi xảy ra.
` error_log() `   Hàm ghi thông tin lỗi vào log
`    ->	   `      Toán tử truy cập phương thức hoặc thuộc tính của đối tượng
` getMessage() `  Lấy nội dung thông báo lỗi
` http_response_code(500) `Hàm này thiết lập HTTP Status Code trả về cho trình duyệt.
` 500 ` 500 Internal Server Error Tức là máy chủ gặp lỗi khi xử lý yêu cầu.
` 200 `	OK – Thành công
` 301 `	Moved Permanently – Chuyển hướng vĩnh viễn
` 302 `	Found – Chuyển hướng
` 400 `	Bad Request – Yêu cầu không hợp lệ
` 404 `	Not Found – Không tìm thấy trang
