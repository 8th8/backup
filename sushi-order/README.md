# 寿司 博多魚がし – Hệ thống order bằng QR

Không cần cài thư viện nào. Chỉ cần Node.js 18 trở lên.

## Chạy

```bash
ADMIN_PASSWORD=matkhau_cua_ban node server.js
```

- Trang khách: http://localhost:3000/?table=5   (5 là số bàn)
- Trang bếp / quản lý: http://localhost:3000/admin.html
- Trang in mã QR: http://localhost:3000/qr.html

## Tạo và in QR cho từng bàn

Mở `/qr.html`, nhập địa chỉ website thật của quán, chọn số bàn (mặc định 24), bấm 作成 rồi 印刷.
Mỗi QR dẫn tới `https://ten-mien/?table=số-bàn`, nên khách không phải nhập số bàn.
Trang này tải thư viện QR từ cdnjs nên máy in cần có internet.

## Thêm ảnh món

Đặt ảnh vào `public/img/` đúng tên khai báo trong `data/menu.json`:
dontaku-set.jpg, ebi.jpg, tai.jpg, choito-ippai-set.jpg, maguro-zukushi.jpg, aka-ebi.jpg, yaki-anago.jpg, sashimi-5.jpg.
Chưa có ảnh thì trang hiện biểu tượng emoji thay thế.

## Sửa menu, giá

Sửa `data/menu.json` (tên món có 5 ngôn ngữ: ja, en, zh-CN, zh-TW, ko) rồi khởi động lại server.
Tạm hết món: dùng mục 売り切れ設定 trong trang quản lý, không cần khởi động lại.

## Đưa lên internet

Cần nơi chạy Node.js có HTTPS (Render, Railway, Fly.io, VPS…). Bắt buộc đặt biến môi trường `ADMIN_PASSWORD`.
Đơn hàng lưu trong `data/orders.json`, nên cần ổ đĩa lưu trữ lâu dài nếu dùng dịch vụ hosting.