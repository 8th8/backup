from scipy.misc import derivative

h = 1e-5
val = derivative(lambda x: x**2 + 1, 1, h)
print(val)