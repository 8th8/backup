# 📚 GHI CHÚ LẬP TRÌNH CSS

### Mã mầu css 
background-color: #FC6C26; background-color: #E46CFF;
background-color: #FFF4D6; background-color: #2E0F35;
background-color: #C8FF3D; background-color: #1A0B2E;
background-color: #2D1B69; background-color: #FFB7A5;
background-color: #B8F7E4; background-color: #C2185B;
background-color: #25272C; background-color: #E0F2FE;
background-color: #CFFF74; background-color: #243B8F;
background-color: #2F3A1D; background-color: #FFF0C9;
background-color: #5B3DF5; background-color: #8FFFE0;
background-color: #E8DEFF; background-color: #FF4696;
background-color: #1E1033; background-color: #064E3B;
z

## Reset CSS — Đặt lại mặc định 
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

img {
    max-width: 100%;
    display: block;
}
# ----------------------------------------------------

## Global CSS — Thiết lập toàn trang ------------------< Quy định font chữ, màu chữ, nền và các thiết lập chung >
body {
    font-family: Arial, sans-serif;
    color: #222;
    background-color: #fff;
    line-height: 1.6;           //đặt chiều cao dòng bằng 1,6 lần kích thước chữ.
}

a {
    color: inherit;            // kế thừa màu chữ từ phần tử cha.
    text-decoration: none;
}

ul {
    list-style: none;
}
# ----------------------------------------------------

## Layout — Khung bố cục chung  -----------------------< Đây là nền tảng để các khu vực trong trang thẳng hàng với nhau >
.wrapper {
    width: min(100% - 32px, 1200px);
    margin-inline: auto;   // tự động căn giữa phần tử theo chiều ngang khi phần tử có chiều rộng nhỏ hơn vùng chứa.
}

.section {
    padding-block: 64px;     
}-
# --------------------------------------------------- 

## Header — Phần đầu trang  ---------------------------< Làm logo, menu điều hướng, nút đăng nhập hoặc nút đặt chỗ.>
.header__inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.header__nav-list {
    display: flex;
    gap: 24px;
}
# ---------------------------------------------------

## Main — Nội dung chính  ---------------------------< Viết theo thứ tự xuất hiện từ trên xuống dưới trong HTML.>
.hero {
    min-height: 480px;
    display: flex;
    align-items: center;
}

.menu__list {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 24px;
}
# ---------------------------------------------------

## Footer — Phần cuối trang  ---------------------------< hiết lập thông tin liên hệ, địa chỉ, giờ mở cửa và các liên kết.>
.footer {
    padding: 48px 0;
    background-color: #222;
    color: #fff;
}
# ---------------------------------------------------

## Responsive — Điều chỉnh màn hình nhỏ  ---------------------------
@media (max-width: 768px) {
    .menu__list {
        grid-template-columns: 1fr;
    }

    .section {
        padding-block: 40px;
    }
}
# ---------------------------------------------------


## ----- CÁC GHI CHÚ HAY  -----------
line-height: 1.2; khoảng cách trên dưới giữa các dòng

## Từ vựng
`align : căn thẳng hàng`
align-items	= Căn chỉnh các phần tử
align-content = Căn chỉnh các nhóm dòng
align-self = Tự căn chỉnh một phần tử
text-align = Căn chỉnh văn bản
------------
`padding : lớp đệm / khoảng trống bên trong phần tử.`
padding: 64px;	Khoảng trống bên trong cả 4 phía
padding-block: 	Phía trên và phía dưới
padding-inline: Bên trái và bên phải
padding-top:    Chỉ phía trên
------------
`margin : lề / khoảng cách bên ngoài phần tử.`
margin-inline: auto; = tự động căn giữa theo chiều ngang
------------
transition :    sự chuyển đổi
display :       hiển thị
flex = linh hoạt
space =         khoảng trống
between =       ở giữa
justify =       căn chỉnh / dàn đều.
justify-content = Căn chỉnh nội dung theo trục chính
justify-items = Căn chỉnh các phần tử bên trong ô
justify-self = Tự căn chỉnh một phần tử
flex-wrap: wrap; = cho phép các phần tử tự động xuống dòng khi không đủ chỗ.
wrap = xuống dòng
nowrap = không xuống dòng
gap = khoảng cách giữa các ô
display: grid; = hiển thị theo bố cục dạng lưới.
grid-template-columns:repeat(3, 1fr); = định nghĩa các cột (chia bố cục thành 3 cột có chiều rộng bằng nhau.)
grid-template-rows = định nghĩa các hàng
overflow = tràn ra ngoài
anywhere = bất kỳ vị trí nào
overflow-wrap: anywhere; = cho phép ngắt dòng ở bất kỳ vị trí nào khi cần để tránh nội dung tràn ra ngoài.