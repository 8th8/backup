
import numpy as np
import matplotlib.pyplot as plt

# 1. Tao du lieu thoi gian
time = np.linspace(0, 20, 1000)

# 2. Tao duong bieu dien con moi (prey)
prey = 2.33 + 1.15 * np.sin(
    2 * np.pi * (time - 0.8) / 5.1
)

# 3. Tao duong bieu dien dong vat san moi (predator)
predator = 2.00 + 1.05 * np.sin(
    2 * np.pi * (time - 1.7) / 5.1
)

# 4. Ve bieu do
plt.figure(figsize=(10, 6))

plt.plot(time, prey, label="prey", color="slateblue")
plt.plot(time, predator, label="predator", color="peru")

# 5. Tieu de va nhan truc
plt.title("Lotka-Volterra equations", fontsize=16)
plt.xlabel("Time", fontsize=12)
plt.ylabel("Population", fontsize=12)

# 6. Cai dat pham vi truc
plt.xlim(0, 20)
plt.ylim(0.85, 3.65)

# 7. Cai dat cac moc chia truc
plt.xticks(np.arange(0, 20.1, 2.5))
plt.yticks(np.arange(1.0, 3.6, 0.5))

# 8. Hien thi luoi va chu thich
plt.grid(True, alpha=0.3)
plt.legend(loc="upper right")

# 9. Can chinh va hien thi
plt.tight_layout()
plt.show()
