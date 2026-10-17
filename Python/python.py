import numpy as np
import matplotlib.pyplot as plt

# データ定義
x = np.array([170, 166, 157, 164, 157, 160, 163, 160, 153, 165]) # 説明変数 身長
y = np.array([57, 53, 47, 52, 47, 53, 48, 48, 43, 57]) # 目的変数 体重

# 説明変数行列Xの定義
ones = np.ones(len(x))
X = np.array([ones, x]).T
# 回帰係数の計算
theta = np.linalg.inv(X.T @ X) @ X.T @ y
print(f'説明変数行列 X =¥n {X}')
print(f'回帰係数 a = {theta[1]}')
print(f'回帰係数 b = {theta[0]}')
print(f'回帰式 W = {theta[1]} * H + {theta[0]}')
# データ散布図と回帰直線 グラフ描画
margin = 1
x_min, x_max = x.min()-margin, x.max()+margin # xの範囲
xx = np.arange(x_min, x_max, 1) # 回帰直線グラフ用変数:xx
# yy = θ_0 + θ_1 * xx # 回帰直線 グラフ用変数:xx,yy
yy = + theta[0] + theta[1] * xx # 回帰直線 グラフ用変数:xx,yy
plt.scatter(x, y, c='Blue') # データを青丸でプロット
plt.plot(xx, yy, c='Red') # 回帰直線を赤線でプロット
plt.xlabel('x:height(cm)')
plt.ylabel('y:weight(kg)')
plt.show()