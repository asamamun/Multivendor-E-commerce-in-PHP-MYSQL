# Multivendor E-Commerce — React Frontend Tutorial

## Table of Contents
1. [Project Overview](#1-project-overview)
2. [Architecture](#2-architecture)
3. [File Structure](#3-file-structure)
4. [Data Flow Diagrams (DFD)](#4-data-flow-diagrams-dfd)
5. [How React Works with the API](#5-how-react-works-with-the-api)
6. [Context & State Management](#6-context--state-management)
7. [Routing](#7-routing)
8. [API Endpoint Reference](#8-api-endpoint-reference)
9. [Authentication Flow](#9-authentication-flow)
10. [Cart Flow](#10-cart-flow)
11. [Order Flow](#11-order-flow)

---

## 1. Project Overview

This is a **React 19 + Vite** single-page application (SPA) that serves as the customer-facing frontend for a multivendor e-commerce platform. The backend is a set of PHP/MySQL REST APIs.

```
Browser (React SPA)  ←→  PHP REST APIs  ←→  MySQL Database
```

**Tech Stack**

| Layer       | Technology                          |
|-------------|-------------------------------------|
| UI          | React 19, Bootstrap 5               |
| Routing     | React Router DOM v7                 |
| HTTP Client | Axios                               |
| State       | React Context API                   |
| Persistence | localStorage (auth token + cart)    |
| Animation   | AOS (Animate On Scroll)             |
| Alerts      | React Toastify                      |
| Build Tool  | Vite 8                              |

---

## 2. Architecture

### High-Level Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                     BROWSER (React SPA)                     │
│                                                             │
│  ┌──────────┐  ┌──────────────┐  ┌──────────────────────┐  │
│  │  Router  │  │   Contexts   │  │       Pages          │  │
│  │          │  │              │  │                      │  │
│  │ /        │  │ AuthContext  │  │ Home, Products,      │  │
│  │ /login   │  │ (token,user) │  │ ProductDetail,       │  │
│  │ /products│  │              │  │ Shops, ShopDetail,   │  │
│  │ /shops   │  │ CartContext  │  │ Cart, Checkout,      │  │
│  │ /cart    │  │ (items,total)│  │ Orders, OrderDetail, │  │
│  │ /checkout│  │              │  │ Dashboard            │  │
│  │ /orders  │  └──────────────┘  └──────────────────────┘  │
│  │ /dashboard│                                              │
│  └──────────┘  ┌──────────────────────────────────────────┐ │
│                │           Components                      │ │
│                │  Navbar, Footer, ProductCard,             │ │
│                │  Pagination, ProtectedRoute               │ │
│                └──────────────────────────────────────────┘ │
│                                                             │
│  ┌──────────────────────────────────────────────────────┐   │
│  │  api/api.js  (Axios instance + Bearer token inject)  │   │
│  └──────────────────────────┬───────────────────────────┘   │
└───────────────────────────── │ ──────────────────────────────┘
                               │  HTTP (JSON)
                               ▼
┌─────────────────────────────────────────────────────────────┐
│                  PHP REST API (backend/apis/)                │
│                                                             │
│   login.php   register.php   products.php                   │
│   shops.php   orders.php                                    │
│                                                             │
│   helpers.php  (CORS, JWT-like token, respond_*)            │
└──────────────────────────┬──────────────────────────────────┘
                           │  SQL
                           ▼
┌─────────────────────────────────────────────────────────────┐
│              MySQL Database (mul_ven_ec_68)                  │
│                                                             │
│  users  products  product_images  categories                │
│  orders  order_items  payments  deliveries  carts           │
└─────────────────────────────────────────────────────────────┘
```

### Layer Responsibilities

| Layer | Responsibility |
|-------|---------------|
| Pages | Fetch data from API, manage local UI state, render layout |
| Components | Reusable UI pieces, receive data via props |
| Contexts | Global shared state (auth session, shopping cart) |
| api/api.js | Single Axios instance, auto-attaches auth token to every request |
| config.js | Central place for API base URL and image URL helper |

---

## 3. File Structure

```
Multivendor-EC-in-react/
├── index.html                  # HTML shell, loads main.jsx
├── vite.config.js              # Vite build config
├── package.json                # Dependencies
│
└── src/
    ├── main.jsx                # Entry point — mounts App, initialises AOS
    ├── App.jsx                 # Root component — BrowserRouter + all Routes
    ├── index.css               # Global styles (hover effects, animations)
    ├── config.js               # API_BASE, ASSETS_BASE, imgUrl() helper
    │
    ├── api/
    │   └── api.js              # Axios instance with auth interceptor
    │
    ├── context/
    │   ├── AuthContext.jsx     # Login/logout state + localStorage sync
    │   └── CartContext.jsx     # Cart CRUD + localStorage sync
    │
    ├── components/
    │   ├── Navbar.jsx          # Top nav with cart badge + user dropdown
    │   ├── Footer.jsx          # Site footer with quick links
    │   ├── ProductCard.jsx     # Reusable product tile (image, price, add-to-cart)
    │   ├── Pagination.jsx      # Page number controls
    │   └── ProtectedRoute.jsx  # Redirects to /login if not authenticated
    │
    └── pages/
        ├── Home.jsx            # Hero + featured products
        ├── Login.jsx           # Login form → POST /login.php
        ├── Register.jsx        # Register form → POST /register.php
        ├── Products.jsx        # Product listing with search + pagination
        ├── ProductDetail.jsx   # Single product, image gallery, add-to-cart
        ├── Shops.jsx           # Vendor shop listing with search + pagination
        ├── ShopDetail.jsx      # Single shop + its products
        ├── Cart.jsx            # Cart items, qty controls, order summary
        ├── Checkout.jsx        # Shipping form + payment → POST /orders.php
        ├── Orders.jsx          # Customer order history (auth required)
        ├── OrderDetail.jsx     # Single order detail + delivery status
        └── Dashboard.jsx       # Customer dashboard with stats + recent orders
```

---

## 4. Data Flow Diagrams (DFD)

### Level 0 — Context Diagram

```
                    ┌─────────────────────┐
                    │                     │
  Customer ────────►│   MarketPlace SPA   │────────► PHP API
  (Browser)         │                     │          (MySQL)
                    └─────────────────────┘
```

---

### Level 1 — Main Processes

```
Customer
   │
   ├──[Browse]──────► 1.0 Product/Shop Discovery ──► GET /products.php
   │                                                  GET /shops.php
   │
   ├──[Auth]────────► 2.0 Authentication ──────────► POST /login.php
   │                                                  POST /register.php
   │                       │
   │                       ▼
   │                  localStorage
   │                  (token, user)
   │
   ├──[Cart]────────► 3.0 Cart Management ──────────► localStorage only
   │                  (no API call for cart)           (client-side)
   │
   ├──[Order]───────► 4.0 Order Processing ─────────► POST /orders.php
   │                                                   GET  /orders.php
   │
   └──[Dashboard]──► 5.0 Customer Dashboard ─────────► GET /orders.php
```

---

### Level 2 — Authentication Flow

```
  ┌──────────┐   email+password   ┌──────────────┐
  │  Login   │ ─────────────────► │ login.php    │
  │  Page    │                    │              │
  └──────────┘                    │ 1. Find user │
       │                          │ 2. Verify pw │
       │   {token, user}          │ 3. Gen token │
       │ ◄────────────────────── └──────────────┘
       │
       ▼
  AuthContext.login()
       │
       ├──► localStorage.setItem('token', token)
       ├──► localStorage.setItem('user', JSON)
       └──► navigate('/dashboard')
```

---

### Level 2 — Product Browsing Flow

```
  ┌──────────────┐  GET ?page=1&search=X  ┌──────────────────┐
  │ Products.jsx │ ──────────────────────► │  products.php    │
  │              │                         │                  │
  │ useEffect()  │                         │ 1. Build WHERE   │
  │ on [page,    │                         │ 2. COUNT total   │
  │  search]     │                         │ 3. SELECT rows   │
  └──────────────┘                         └──────────────────┘
         │                                          │
         │  {products[], pagination{}}              │
         │ ◄────────────────────────────────────────┘
         │
         ▼
  setProducts(rows)
  setPagination(meta)
         │
         ▼
  Render <ProductCard /> × N
  Render <Pagination />
```

---

### Level 2 — Cart & Checkout Flow

```
  ProductCard / ProductDetail
         │
         │ addToCart(product, qty)
         ▼
  CartContext (in-memory + localStorage)
         │
         │ user clicks "Checkout"
         ▼
  Checkout.jsx
         │
         │ fills shipping form + picks payment method
         │
         │ POST /orders.php
         │  body: { items[], shipping_address, payment_method, notes }
         │  header: Authorization: Bearer <token>
         ▼
  orders.php
         │
         ├── Validate items & stock
         ├── Calculate totals (subtotal + ৳60 shipping)
         ├── INSERT orders
         ├── INSERT order_items
         ├── UPDATE products.stock_quantity
         └── INSERT payments
         │
         │ {order_id, order_number, total_amount}
         ▼
  clearCart()
  toast.success(order_number)
  navigate('/orders')
```

---

### Level 2 — Order History Flow

```
  Orders.jsx / Dashboard.jsx
         │
         │ GET /orders.php?page=1
         │ header: Authorization: Bearer <token>
         ▼
  orders.php
         │
         ├── require_auth() — verify token
         ├── SELECT orders WHERE customer_id = ?
         └── Decode shipping_address JSON
         │
         │ {orders[], pagination{}}
         ▼
  Render order list

  User clicks "View Details"
         │
         │ GET /orders.php?id=X
         ▼
  orders.php
         │
         ├── SELECT order + delivery status
         ├── SELECT order_items + product images
         └── Decode address JSON
         │
         │ {order{items[], shipping_address, delivery_status}}
         ▼
  OrderDetail.jsx renders full breakdown
```

---

## 5. How React Works with the API

### The Axios Instance (`src/api/api.js`)

Every API call goes through a single Axios instance. A **request interceptor** automatically reads the token from localStorage and attaches it as a Bearer header — so individual pages never need to handle auth headers manually.

```js
// src/api/api.js
const api = axios.create({ baseURL: API_BASE });

api.interceptors.request.use(config => {
  const token = localStorage.getItem('token');
  if (token) config.headers.Authorization = `Bearer ${token}`;
  return config;
});
```

### Typical Page Data Fetch Pattern

Every page that needs data follows the same pattern:

```jsx
useEffect(() => {
  setLoading(true);
  api.get('/products.php?page=1')
    .then(res => {
      // API always returns: { success, message, data: {...} }
      setProducts(res.data.data.products);
    })
    .catch(() => {})
    .finally(() => setLoading(false));
}, [page, search]);  // re-runs when deps change
```

### API Response Shape

All PHP endpoints return the same envelope:

```json
{
  "success": true,
  "message": "OK",
  "data": {
    "products": [...],
    "pagination": { "page": 1, "limit": 12, "total": 50, "total_pages": 5 }
  }
}
```

On error:
```json
{
  "success": false,
  "message": "Invalid email or password"
}
```

React reads `res.data.data` for the payload and `err.response.data.message` for error messages shown via toast.

---

## 6. Context & State Management

### AuthContext

Wraps the entire app. Stores `user` object and `token` string. Both are persisted to localStorage so the session survives page refresh.

```
AuthContext
  ├── user        { id, name, email, phone, role }
  ├── token       "base64payload.hmac_sig"
  ├── isLoggedIn  boolean
  ├── login(user, token)   → saves to state + localStorage
  └── logout()             → clears state + localStorage
```

**Consumed by:** Navbar (show user name / logout), ProtectedRoute (redirect if not logged in), Checkout (pre-fill name/phone), Dashboard (show welcome message).

### CartContext

Manages the shopping cart entirely on the client side — no cart API calls. State is synced to localStorage on every change via `useEffect`.

```
CartContext
  ├── cart[]      [{ id, name, price, image, vendor, qty }]
  ├── count       total item count (for badge)
  ├── total       subtotal in BDT
  ├── addToCart(product, qty)
  ├── removeFromCart(id)
  ├── updateQty(id, qty)
  └── clearCart()          → called after successful order
```

**Consumed by:** Navbar (badge count), ProductCard, ProductDetail, Cart, Checkout, Dashboard.

---

## 7. Routing

Routes are defined in `App.jsx`. Protected routes wrap pages that require login.

| Path | Component | Auth Required |
|------|-----------|:---:|
| `/` | Home | No |
| `/login` | Login | No |
| `/register` | Register | No |
| `/products` | Products | No |
| `/products/:id` | ProductDetail | No |
| `/shops` | Shops | No |
| `/shops/:id` | ShopDetail | No |
| `/cart` | Cart | No |
| `/checkout` | Checkout | Yes |
| `/orders` | Orders | Yes |
| `/orders/:id` | OrderDetail | Yes |
| `/dashboard` | Dashboard | Yes |

`ProtectedRoute` checks `isLoggedIn` from AuthContext. If false, it redirects to `/login` using React Router's `<Navigate>`.

---

## 8. API Endpoint Reference

### Base URL
```
http://localhost/round68/reactJS/projects/backend/apis/
```

### Assets Base URL
```
http://localhost/round68/reactJS/projects/Multivendor-E-commerce-in-PHP-MYSQL/
```
Product images are stored as relative paths like `assets/uploads/products/filename.jpg`. The `imgUrl()` helper in `config.js` prepends the assets base URL.

---

### POST `/login.php`
**Body:** `{ email, password }`  
**Returns:** `{ token, user: { id, name, email, phone, role } }`

---

### POST `/register.php`
**Body:** `{ name, email, phone, password, role }`  
role options: `customer | vendor | courier`  
**Returns:** `{ token, user }`

---

### GET `/products.php`
**Query params:**

| Param | Type | Description |
|-------|------|-------------|
| page | int | Page number (default 1) |
| limit | int | Items per page (default 12, max 50) |
| search | string | Keyword search on name/description |
| category | int | Filter by category ID |
| vendor | int | Filter by vendor ID |
| featured | 1 | Featured products only |
| id | int | Single product detail |

**Returns (list):** `{ products[], pagination{} }`  
**Returns (single):** `{ product{ ...fields, images[], primary_image } }`

---

### GET `/shops.php`
**Query params:**

| Param | Type | Description |
|-------|------|-------------|
| page | int | Page number |
| limit | int | Items per page |
| search | string | Search by vendor name |
| id | int | Single shop detail |

**Returns (list):** `{ shops[], pagination{} }`  
**Returns (single):** `{ shop{ vendor_id, vendor_name, email, phone, products[] } }`

---

### GET `/orders.php` *(auth required)*
**Query params:** `page`, `limit`, `id` (single order)  
**Returns (list):** `{ orders[], pagination{} }`  
**Returns (single):** `{ order{ ...fields, items[], shipping_address, delivery_status } }`

---

### POST `/orders.php` *(auth required)*
**Body:**
```json
{
  "items": [{ "product_id": 1, "quantity": 2 }],
  "shipping_address": { "name": "", "phone": "", "address": "", "city": "", "zip": "" },
  "payment_method": "cod | bkash | nagad | rocket",
  "notes": ""
}
```
**Returns:** `{ order_id, order_number, total_amount, item_count }`

---

## 9. Authentication Flow

```
1. User submits login form
        │
        ▼
2. POST /login.php  →  PHP verifies password_hash()
        │
        ▼
3. PHP generates token:
   payload = base64({ user_id, role, email, exp })
   token   = payload + "." + HMAC-SHA256(payload, secret)
        │
        ▼
4. React stores token in localStorage
        │
        ▼
5. Every subsequent API call:
   Axios interceptor reads token → adds Authorization: Bearer <token>
        │
        ▼
6. PHP require_auth() on protected endpoints:
   - Splits token on "."
   - Re-computes HMAC, compares with hash_equals()
   - Decodes payload, checks exp timestamp
   - Returns payload (contains user_id) or 401
        │
        ▼
7. Logout: localStorage cleared, context reset, redirect to /
```

---

## 10. Cart Flow

The cart is **100% client-side** — no database calls until checkout.

```
User clicks "Add to Cart"
        │
        ▼
CartContext.addToCart(product, qty)
        │
        ├── If product already in cart → increment qty
        └── Else → append new item
        │
        ▼
useEffect syncs cart[] → localStorage('mv_cart')
        │
        ▼
Navbar badge re-renders with new count
        │
        ▼
User visits /cart → reads from CartContext (already in memory)
        │
        ▼
User adjusts qty → updateQty(id, qty)
User removes item → removeFromCart(id)
        │
        ▼
User clicks "Checkout" → navigate('/checkout')
  (if not logged in → navigate('/login') first)
```

---

## 11. Order Flow

```
Checkout.jsx
  │
  │  User fills:
  │  - Shipping address (name, phone, address, city, zip)
  │  - Payment method (cod / bkash / nagad / rocket)
  │  - Optional notes
  │
  │  POST /orders.php
  │  Authorization: Bearer <token>
  │  Body: { items, shipping_address, payment_method, notes }
  │
  ▼
orders.php (PHP)
  │
  ├── require_auth() → get customer_id from token
  ├── Validate each product (active, in stock)
  ├── Calculate: subtotal + ৳60 shipping = total
  ├── BEGIN TRANSACTION
  │     INSERT orders
  │     INSERT order_items (one row per product)
  │     UPDATE products SET stock_quantity = stock_quantity - qty
  │     INSERT payments
  │   COMMIT
  │
  │  Returns: { order_id, order_number, total_amount }
  │
  ▼
React:
  ├── clearCart()
  ├── toast.success("Order ORD-XXXX placed!")
  └── navigate('/orders')
```

---

*Content was written based on the actual source code in this repository.*


---

## 12. Known Issues & Fixes

### "Unauthorized" on Checkout / Place Order (XAMPP/Apache)

**Symptom:** The customer is logged in, the cart works, but clicking "Place Order" shows an `Unauthorized` toast even though the token is present in localStorage.

**Root cause:** Apache (used by XAMPP) silently strips the `Authorization` header before it reaches PHP. This means `$_SERVER['HTTP_AUTHORIZATION']` is empty, so `require_auth()` never sees the Bearer token and returns 401.

React sends the request correctly:
```
POST /apis/orders.php
Authorization: Bearer eyJ1c2VyX2lkIjo...
```

But Apache drops the header, so PHP receives:
```
POST /apis/orders.php
(no Authorization header)
```

**Fix 1 — `.htaccess` in `backend/apis/`**

Create `backend/apis/.htaccess` with a rewrite rule that explicitly passes the header into the PHP environment:

```apache
RewriteEngine On

# Pass the Authorization header to PHP — Apache strips it by default
RewriteCond %{HTTP:Authorization} .
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
```

**Fix 2 — Multi-source fallback in `helpers.php`**

Even with `.htaccess`, Apache can populate different `$_SERVER` keys depending on the setup (CGI, FastCGI, mod_php). The `require_auth()` function was updated to check all three possible locations:

```php
function get_auth_header(): string {
    // 1. Standard key (works with mod_php when .htaccess rule is present)
    if (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
        return $_SERVER['HTTP_AUTHORIZATION'];
    }
    // 2. Set by Apache after a mod_rewrite redirect
    if (!empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
        return $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
    }
    // 3. CGI / FastCGI environments
    if (function_exists('getallheaders')) {
        foreach (getallheaders() as $name => $value) {
            if (strtolower($name) === 'authorization') {
                return $value;
            }
        }
    }
    return '';
}

function require_auth(): array {
    $header = get_auth_header();
    if (preg_match('/Bearer\s+(.+)/i', $header, $m)) {
        $payload = verify_token($m[1]);
        if ($payload) return $payload;
    }
    respond_error('Unauthorized', 401);
    exit();
}
```

**Files changed:**
- `backend/apis/.htaccess` — created (new file)
- `backend/apis/helpers.php` — `require_auth()` replaced with `get_auth_header()` + `require_auth()`

**Why the React side was fine:** The Axios interceptor in `api/api.js` was always attaching the token correctly. The problem was entirely on the server side — Apache never forwarded it to PHP.
