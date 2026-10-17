# Ghi_chu Python

### cài thư viện trên TERMINAL

vào đúng thư mục (PS C:\xampp\htdocs\Python> ) -> 
`    python -m pip install matplotlib    `

### 
``` đọc file csv ---
----------------------------------------------------------------
import pandas as pd

df = pd.read_csv("dazaifu_utf8.csv", encoding="utf-8-sig")

print(df)
----------------------------------------------------------------
```
-`read_csv()` : Hàm đọc dữ liệu từ file CSV
-`index_col=0` : Chọn cột đầu tiên làm Index (chỉ mục của các hàng)
- lưu ý : nếu là utf8 thì phải đổi thành  `encoding="utf-8-sig"`
còn nếu không phải utf8 thì để `encoding="Shift_JIS"`

```
import pandas as pd

df1 = pd.read_csv("titanic.csv", index_col=0)

df2 = pd.DataFrame()                            
df2["Age"] = df1["Age"]

print(df2 >= 20)
```
-`DataFrame()` : Hàm tạo bảng dữ liệu