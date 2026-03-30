# Context API Tutorial — MarketPlace App

## What is Context API?

React's Context API solves **prop drilling** — the problem of passing data through many component layers just to reach a deeply nested child. Instead of threading props down the tree, you put shared state in a Context and any component in the tree can read it directly.

```
Without Context (prop drilling):
App → Navbar → UserMenu → Avatar  (user passed at every level)

With Context:
App (AuthProvider)
  └── Avatar  →  useAuth()  ✓  (reads directly, no props needed)
```

---

## Architecture Overview

```
main.jsx
  └── App.jsx
        └── <BrowserRouter>
              └── <AuthProvider>          ← AuthContext lives here
                    └── <CartProvider>    ← CartContext lives here
                          ├── <Navbar />
                          ├── <main>
                          │     ├── <Login />
                          │     ├── <Register />
                          │     ├── <ProductDetail />
                          │     ├── <Cart />
                          │     ├── <Checkout />       (ProtectedRoute)
                          │     ├── <Dashboard />      (ProtectedRoute)
                          │     └── <Orders />         (ProtectedRoute)
                          └── <Footer />
```

Both providers wrap the entire app in `App.jsx`, so every component in the tree can access auth and cart state without receiving any props.

---

## AuthContext

### Implementation (`src/context/AuthContext.jsx`)

```jsx
import { createContext, useContext, useState } from 'react';

const AuthContext = createContext(null);   // 1. Create the context

export function AuthProvider({ children }) {
  // 2. State is initialized from localStorage (persists on refresh)
  const [user, setUser]   = useState(() => JSON.parse(localStorage.getItem('user')));
  const [token, setToken] = useState(() => localStorage.getItem('token') || null);

  const login = (userData, tokenData) => {
    setUser(userData);
    setToken(tokenData);
    localStorage.setItem('user',  JSON.stringify(userData));
    localStorage.setItem('token', tokenData);
  };

  const logout = () => {
    setUser(null);
    setToken(null);
    localStorage.removeItem('user');
    localStorage.removeItem('token');
  };

  // 3. Expose value to all children
  return (
    <AuthContext.Provider value={{ user, token, isLoggedIn: !!token, login, logout }}>
      {children}
    </AuthContext.Provider>
  );
}

// 4. Custom hook — the clean way to consume this context
export const useAuth = () => useContext(AuthContext);
```

### What AuthContext exposes

| Value | Type | Description |
|---|---|---|
| `user` | `{ id, name, email, phone, role }` | Logged-in user object, or `null` |
| `token` | `string \| null` | JWT/base64 token for API requests |
| `isLoggedIn` | `boolean` | Derived from `!!token` |
| `login(user, token)` | function | Saves to state + localStorage |
| `logout()` | function | Clears state + localStorage |

### Data flow — Login

```
Login.jsx
  │
  ├── calls api.post('/login.php', form)
  │
  ├── receives { user, token } from server
  │
  └── calls login(user, token)
        │
        ├── setUser(userData)      → re-renders all useAuth() consumers
        ├── setToken(tokenData)
        ├── localStorage.setItem('user', ...)
        └── localStorage.setItem('token', ...)
```

After `login()` is called, every component using `useAuth()` re-renders automatically with the new values — no props passed anywhere.

---

## CartContext

### Implementation (`src/context/CartContext.jsx`)

```jsx
import { createContext, useContext, useState, useEffect } from 'react';

const CartContext = createContext(null);
const CART_KEY = 'mv_cart';

export function CartProvider({ children }) {
  // Initialized from localStorage
  const [cart, setCart] = useState(() => JSON.parse(localStorage.getItem(CART_KEY)) || []);

  // Sync to localStorage whenever cart changes
  useEffect(() => {
    localStorage.setItem(CART_KEY, JSON.stringify(cart));
  }, [cart]);

  const addToCart = (product, qty = 1) => {
    setCart(prev => {
      const existing = prev.find(i => i.id === product.id);
      if (existing) {
        // Increment qty if already in cart
        return prev.map(i => i.id === product.id ? { ...i, qty: i.qty + qty } : i);
      }
      return [...prev, { ...product, qty }];
    });
  };

  const removeFromCart = (id) => setCart(prev => prev.filter(i => i.id !== id));

  const updateQty = (id, qty) => {
    if (qty < 1) return removeFromCart(id);   // auto-remove at 0
    setCart(prev => prev.map(i => i.id === id ? { ...i, qty } : i));
  };

  const clearCart = () => setCart([]);

  // Derived values — computed on every render, no extra state needed
  const count = cart.reduce((sum, i) => sum + i.qty, 0);
  const total = cart.reduce((sum, i) => sum + i.price * i.qty, 0);

  return (
    <CartContext.Provider value={{ cart, addToCart, removeFromCart, updateQty, clearCart, count, total }}>
      {children}
    </CartContext.Provider>
  );
}

export const useCart = () => useContext(CartContext);
```

### What CartContext exposes

| Value | Type | Description |
|---|---|---|
| `cart` | `Array<{ id, name, price, image, vendor, qty }>` | All cart items |
| `count` | `number` | Total item quantity (used for badge) |
| `total` | `number` | Subtotal in BDT |
| `addToCart(product, qty)` | function | Adds or increments item |
| `removeFromCart(id)` | function | Removes item by id |
| `updateQty(id, qty)` | function | Updates qty, removes if qty < 1 |
| `clearCart()` | function | Empties cart (called after order placed) |

---

## Who Uses Each Context

### AuthContext consumers

| File | Hook | What it reads/calls |
|---|---|---|
| `components/Navbar.jsx` | `useAuth()` | `user.name`, `isLoggedIn`, `logout()` |
| `components/ProtectedRoute.jsx` | `useAuth()` | `isLoggedIn` — redirects to `/login` if false |
| `pages/Login.jsx` | `useAuth()` | `login(user, token)` — called after successful API response |
| `pages/Register.jsx` | `useAuth()` | `login(user, token)` — auto-login after registration |
| `pages/Cart.jsx` | `useAuth()` | `isLoggedIn` — guards checkout navigation |
| `pages/Checkout.jsx` | `useAuth()` | `user.name`, `user.phone` — pre-fills shipping form |
| `pages/Dashboard.jsx` | `useAuth()` | `user.name`, `user.email` — welcome banner |

### CartContext consumers

| File | Hook | What it reads/calls |
|---|---|---|
| `components/Navbar.jsx` | `useCart()` | `count` — cart badge number |
| `components/ProductCard.jsx` | `useCart()` | `addToCart()` — quick add from listing |
| `pages/ProductDetail.jsx` | `useCart()` | `addToCart()` — add with selected qty |
| `pages/Cart.jsx` | `useCart()` | `cart`, `total`, `removeFromCart()`, `updateQty()`, `clearCart()` |
| `pages/Checkout.jsx` | `useCart()` | `cart`, `total`, `clearCart()` — order summary + clear on success |
| `pages/Dashboard.jsx` | `useCart()` | `count`, `total` — stat cards |

---

## Why No Prop Drilling?

Take `ProductCard` as a concrete example. It lives inside `Products.jsx` which is rendered by `App.jsx`. Without Context, `addToCart` would need to be passed like this:

```
App → Products → ProductCard   (addToCart passed as prop at each level)
```

With Context, `ProductCard` just calls `useCart()` directly:

```jsx
// ProductCard.jsx — no props needed for cart functionality
export default function ProductCard({ product }) {
  const { addToCart } = useCart();   // reads directly from context
  // ...
}
```

The `product` prop is still passed because it's data specific to that card instance. But shared global state (cart, auth) comes from context.

---

## ProtectedRoute — Auth Guard Pattern

```jsx
// components/ProtectedRoute.jsx
export default function ProtectedRoute({ children }) {
  const { isLoggedIn } = useAuth();
  return isLoggedIn ? children : <Navigate to="/login" replace />;
}
```

Used in `App.jsx` to wrap private routes:

```jsx
<Route path="/checkout" element={<ProtectedRoute><Checkout /></ProtectedRoute>} />
<Route path="/dashboard" element={<ProtectedRoute><Dashboard /></ProtectedRoute>} />
<Route path="/orders"    element={<ProtectedRoute><Orders /></ProtectedRoute>} />
<Route path="/orders/:id" element={<ProtectedRoute><OrderDetail /></ProtectedRoute>} />
```

`isLoggedIn` is `!!token` — a boolean derived from context state. No prop is passed to `ProtectedRoute`; it reads auth state itself.

---

## localStorage Persistence

Both contexts persist their state to localStorage so data survives page refreshes.

```
AuthContext
  login()  → localStorage.setItem('user', ...)  +  localStorage.setItem('token', ...)
  logout() → localStorage.removeItem('user')    +  localStorage.removeItem('token')
  init     → useState(() => JSON.parse(localStorage.getItem('user')))

CartContext
  useEffect([cart]) → localStorage.setItem('mv_cart', JSON.stringify(cart))
  init              → useState(() => JSON.parse(localStorage.getItem('mv_cart')) || [])
```

The lazy initializer pattern `useState(() => ...)` means localStorage is only read once on mount, not on every render.

---

## Key Concepts Summary

| Concept | How it's used here |
|---|---|
| `createContext(null)` | Creates the context object with a default of `null` |
| `<Context.Provider value={...}>` | Wraps the tree and injects the shared value |
| `useContext(Context)` | Reads the value inside any child component |
| Custom hook (`useAuth`, `useCart`) | Wraps `useContext` for cleaner imports and reuse |
| Lazy `useState` initializer | Reads localStorage once on mount for persistence |
| Derived state (`count`, `total`, `isLoggedIn`) | Computed from existing state, no extra `useState` needed |
