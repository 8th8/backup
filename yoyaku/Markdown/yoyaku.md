=====================================================   
               STEP 1
            reservation.php 
        Nhập thông tin khách hàng
                  |
                POST
                  |
                STEP 2
        reservation_confirm.php
 Hiển thị thông tin xác nhận + hidden input
                  |
                 POST
                  |
                STEP 3
        reservation_insert.php
 Kiểm tra dữ liệu → INSERT vào MySQL
                  |
Redirect sau khi INSERT thành công
                  |
                STEP 4
        reservation_complete.php
Hiển thị thông báo đặt bàn thành công
=====================================================