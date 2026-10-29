import pandas as pd
import matplotlib.pyplot as plt
import seaborn as sns
from matplotlib.patches import Ellipse
from matplotlib.cbook import boxplot_stats

# 日本語フォント
plt.rcParams["font.family"] = "Meiryo"

# データの読み込み
df = pd.read_csv("titanic.csv", index_col=0)
df = df[df["Sex"].isin(["male", "female"])].copy()
df["Age"] = pd.to_numeric(df["Age"], errors="coerce")

# グラフの作成
fig, ax = plt.subplots(figsize=(10, 7))

sns.boxplot(
    data=df,
    x="Sex",
    y="Age",
    order=["male", "female"],
    palette=["#3274A1", "#E1812C"],
    width=0.5,
    showfliers=False,  # 外れ値（黒い点）を非表示にする
    ax=ax
)

# 軸ラベル
ax.set_xlabel("Sex", fontsize=14)
ax.set_ylabel("The number of people", fontsize=14)
ax.set_ylim(-4, 84)

# 男性データの四分位数などを計算
male_age = df.loc[df["Sex"] == "male", "Age"].dropna()
stats = boxplot_stats(male_age, whis=1.5)[0]

plt.tight_layout()
plt.show()