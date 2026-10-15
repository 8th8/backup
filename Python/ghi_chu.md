# Ghi_chu Python

### 
``` đọc file csv
import pandas as pd

df = pd.read_csv("dazaifu_utf8.csv", encoding="utf-8-sig")

print(df)
```
- lưu ý : nếu là utf8 thì phải đổi thành  `encoding="utf-8-sig"`
còn nếu không phải utf8 thì để `encoding="Shift_JIS"`