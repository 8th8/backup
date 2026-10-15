import pandas as pd

df = pd.read_csv("dazaifu.csv", encoding="Shift_JIS", index_col=0)

print(df.loc["2024/4/5", "最高気温(℃)"])
