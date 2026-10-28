# 📚 GHI CHÚ LẬP TRÌNH CSS

## 1. CSS Reset — Đặt lại mặc định -----------------------------------------------------------------------

Xóa các khoảng cách mặc định của trình duyệt và giúp việc tính toán kích thước phần tử dễ dàng hơn.

```css
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

img {
    max-width: 100%;
    display: block;
}
```

- `margin: 0;` — Xóa lề mặc định.
- `padding: 0;` — Xóa khoảng đệm mặc định.
- `box-sizing: border-box;` — Tính cả padding và border vào kích thước đã khai báo.
- `max-width: 100%;` — Giới hạn chiều rộng ảnh không vượt quá phần tử cha.
- `display: block;` — Hiển thị ảnh dưới dạng phần tử block.

---

## 2. Global CSS — Thiết lập toàn trang ------------------------------------------------------------------

Quy định font chữ, màu chữ, màu nền và cách hiển thị chung.

```css
body {
    font-family: Arial, sans-serif;
    color: #222;
    background-color: #fff;
    line-height: 1.6;
}

a {
    color: inherit;
    text-decoration: none;
}

ul {
    list-style: none;
}
```

- `font-family` — Font chữ.
- `color` — Màu chữ.
- `background-color` — Màu nền.
- `line-height: 1.6;` — Chiều cao dòng bằng 1,6 lần kích thước chữ.
- `color: inherit;` — Kế thừa màu chữ từ phần tử cha.
- `text-decoration: none;` — Bỏ gạch chân mặc định của liên kết.
- `list-style: none;` — Bỏ dấu đầu dòng của danh sách.

---

## 3. Layout — Khung bố cục chung

Thiết lập chiều rộng nội dung và khoảng cách giữa các khu vực lớn của trang web.

```css
.wrapper {
    width: min(100% - 32px, 1200px);
    margin-inline: auto;
}

.section {
    padding-block: 64px;
}
```

- `.wrapper` — Khung giới hạn chiều rộng nội dung.
- `width: min(100% - 32px, 1200px);` — Chiều rộng tối đa 1200px, đồng thời chừa khoảng trống hai bên khi màn hình nhỏ.
- `margin-inline: auto;` — Tự động căn giữa theo chiều ngang.
- `padding-block: 64px;` — Tạo khoảng đệm phía trên và dưới.
- `.section` — Class thường dùng cho các khu vực nội dung lớn.

---

## 4. Header — Phần đầu trang

Thường chứa logo, menu điều hướng, nút đăng nhập hoặc nút đặt chỗ.

```css
.header__inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.header__nav-list {
    display: flex;
    gap: 24px;
}
```

- `display: flex;` — Sắp xếp phần tử theo Flexbox.
- `align-items: center;` — Căn các phần tử theo trục dọc trong hàng Flexbox.
- `justify-content: space-between;` — Đẩy phần tử đầu và cuối ra hai phía, phân bố khoảng trống ở giữa.
- `gap: 24px;` — Khoảng cách giữa các mục menu.

---

## 5. Main — Nội dung chính

Nên tổ chức CSS theo thứ tự các khu vực xuất hiện từ trên xuống dưới trong HTML.

### 5.1. Hero — Khu vực giới thiệu nổi bật

```css
.hero {
    min-height: 480px;
    display: flex;
    align-items: center;
}
```

- `min-height: 480px;` — Chiều cao tối thiểu 480px.
- `display: flex;` — Dùng Flexbox để bố trí nội dung.
- `align-items: center;` — Căn nội dung vào giữa theo trục dọc.

### 5.2. List / Grid — Danh sách sản phẩm hoặc món ăn

```css
.menu__list {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 24px;
}
```

- `display: grid;` — Sử dụng bố cục Grid.
- `grid-template-columns` — Xác định số lượng và kích thước các cột.
- `repeat(3, ...)` — Lặp lại cấu hình 3 cột.
- `1fr` — Một phần của không gian còn lại.
- `minmax(0, 1fr)` — Cho phép cột co nhỏ phù hợp, hạn chế tràn nội dung.
- `gap: 24px;` — Khoảng cách giữa các ô.

---

## 6. Footer — Phần cuối trang

Thường chứa thông tin liên hệ, địa chỉ, giờ mở cửa và các liên kết.

```css
.footer {
    padding: 48px 0;
    background-color: #222;
    color: #fff;
}
```

- `padding: 48px 0;` — Trên và dưới 48px, trái và phải 0.
- `background-color: #222;` — Nền màu xám đậm.
- `color: #fff;` — Chữ màu trắng.

---

## 7. Responsive — Điều chỉnh theo kích thước màn hình

Sử dụng Media Query để thay đổi bố cục trên điện thoại và máy tính bảng.

```css
@media (max-width: 768px) {
    .menu__list {
        grid-template-columns: 1fr;
    }

    .section {
        padding-block: 40px;
    }
}
```

- `@media` — Áp dụng CSS theo điều kiện màn hình.
- `max-width: 768px` — Áp dụng khi viewport rộng tối đa 768px.
- `grid-template-columns: 1fr;` — Chuyển danh sách thành một cột.
- `padding-block: 40px;` — Giảm khoảng đệm trên và dưới để phù hợp màn hình nhỏ.

---

## 8. Background — Màu sắc CSS

### 8.1. Cách sử dụng

```css
.example {
    background-color: #FC6C26;
}
```

### 8.2. Bảng màu tham khảo

| Mã màu    | Gợi ý tên màu |
|-------|---|
| #FC6C26 | Cam đậm |
| #E46CFF | Tím hồng sáng |
| #FFF4D6 | Kem nhạt |
| #2E0F35 | Tím than |
| #C8FF3D | Xanh chanh |
| #1A0B2E | Tím đen |
| #2D1B69 | Tím đậm |
| #FFB7A5 | Hồng đào |
| #B8F7E4 | Xanh bạc hà nhạt |
| #C2185B | Hồng đậm |
| #25272C | Xám đen |
| #E0F2FE | Xanh da trời rất nhạt |
| #CFFF74    | Xanh lá sáng |
| #243B8F | Xanh dương đậm |
| #2F3A1D | Xanh olive đậm |
| #FFF0C9 | Vàng kem |
| #5B3DF5 | Tím xanh sáng |
| #8FFFE0 | Xanh ngọc sáng |
| #E8DEFF | Tím pastel |
| #FF4696 | Hồng sáng |
| #1E1033 | Tím than đậm |
| #064E3B | Xanh lá đậm |

---

## 9. Các thuộc tính CSS thường dùng

### 9.1. Spacing — Khoảng cách

#### Padding — Khoảng đệm bên trong

```css
padding: 64px;
padding-block: 64px;
padding-inline: 32px;
padding-top: 16px;
```

- `padding` — Khoảng đệm cả bốn phía.
- `padding-block` — Khoảng đệm trên và dưới.
- `padding-inline` — Khoảng đệm trái và phải.
- `padding-top` — Chỉ phía trên.

#### Margin — Lề bên ngoài

```css
margin: 20px;
margin-inline: auto;
margin-bottom: 24px;
```

- `margin` — Khoảng cách bên ngoài phần tử.
- `margin-inline: auto;` — Tự động căn giữa theo chiều ngang khi phần tử có chiều rộng phù hợp.
- `margin-bottom` — Khoảng cách phía dưới.

### 9.2. Alignment — Căn chỉnh

- `align-items` — Căn chỉnh các phần tử theo trục phụ của Flexbox/Grid.
- `align-content` — Căn chỉnh tổng thể các hàng hoặc cột khi có không gian dư.
- `align-self` — Căn chỉnh riêng một phần tử theo trục phụ.
- `text-align` — Căn chỉnh văn bản.
- `justify-content` — Phân bố hoặc căn chỉnh nội dung theo trục chính của Flexbox.
- `justify-items` — Căn chỉnh nội dung bên trong các ô Grid.
- `justify-self` — Căn chỉnh riêng một phần tử trong ô Grid.

### 9.3. Display & Layout — Hiển thị và bố cục

- `display` — Kiểu hiển thị của phần tử.
- `display: block;` — Hiển thị dạng block.
- `display: flex;` — Bố cục Flexbox.
- `display: grid;` — Bố cục Grid.
- `gap` — Khoảng cách giữa các phần tử hoặc ô.
- `grid-template-columns` — Định nghĩa các cột.
- `grid-template-rows` — Định nghĩa các hàng.

Ví dụ:

```css
.container {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
}
```

### 9.4. Flexbox — Bố cục linh hoạt

- `flex` — Thuộc tính viết tắt để thiết lập khả năng co giãn của phần tử Flexbox.
- `flex-wrap: wrap;` — Cho phép phần tử xuống dòng khi thiếu chỗ.
- `flex-wrap: nowrap;` — Không cho phép xuống dòng.
- `justify-content: space-between;` — Phân bố phần tử với khoảng trống giữa chúng.

### 9.5. Text & Overflow — Văn bản và tràn nội dung

- `line-height: 1.2;` — Chiều cao dòng bằng 1,2 lần kích thước chữ.
- `overflow` — Xử lý nội dung vượt ra ngoài hộp.
- `overflow-wrap: anywhere;` — Cho phép ngắt chuỗi dài tại vị trí cần thiết để tránh tràn.
- `anywhere` — Bất kỳ vị trí nào phù hợp để ngắt dòng.
- `transition` — Tạo hiệu ứng chuyển đổi giữa các trạng thái CSS.

Ví dụ:

```css
.card {
    transition: 0.3s;
}

.long-text {
    overflow-wrap: anywhere;
}
```

---

## 10. Từ vựng CSS — Tiếng Anh và tiếng Việt

|   Từ vựng     | Nghĩa |
|---------------|------------|
| `align`       | Căn chỉnh |
| `justify`     | Căn chỉnh, phân bố |
| `display`     | Hiển thị |
| `flex`        | Linh hoạt |
| `grid`        | Lưới |
| `space`       | Khoảng trống |
| `between`     | Ở giữa, giữa các phần tử |
| `wrap`        | Xuống dòng |
| `nowrap`      | Không xuống dòng |
| `gap`         | Khoảng cách |
| `padding`     | Khoảng đệm bên trong |
| `margin`      | Lề bên ngoài |
| `background`  | Nền |
| `color`       | Màu sắc |
| `width`       | Chiều rộng |
| `height`      | Chiều cao |
| `min`         | Tối thiểu |
| `max`         | Tối đa |
| `overflow`    | Tràn ra ngoài |
| `transition`  | Chuyển đổi, hiệu ứng chuyển tiếp |
| `inherit`     | Kế thừa |
| `content`     | Nội dung |
| `column`      | Cột |
| `row`         | Hàng |
| `responsive`  | Thích ứng với kích thước màn hình |

---

## 11. Thứ tự viết CSS khi bắt đầu một trang web

1. **Reset** — Đặt lại mặc định. ()
2. **Global** — Font chữ, màu chữ, nền và quy tắc chung.
3. **Layout** — Wrapper, container, section.
4. **Header** — Logo, navigation, button.
5. **Main** — Hero, content, card, list, form...
6. **Footer** — Thông tin cuối trang.
7. **Responsive** — Điều chỉnh cho tablet và mobile.

> Mẹo: Không bắt buộc mọi dự án phải theo đúng thứ tự này, nhưng đây là cách tổ chức dễ đọc, dễ bảo trì và thuận tiện khi tự học CSS.
