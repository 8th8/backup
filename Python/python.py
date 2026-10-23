import numpy as np
import math
import matplotlib.pyplot as plt

t = np.arange(0,1000,0.1)

y_0 = 100
k = 0.01
c = 100000
y = y_0 * c * math.e**(k *t) / c - y_0 + y_0 * math.e**(k *t)

plt.plot(t,y)
plt.title('Logistic equation')
plt.xlabel("time")
plt.ylabel('Population')
plt.show()
