# Multi-Vendor E-Commerce Platform – Technical Specification

## 1. Project Overview
A lightweight, scalable multi-vendor e-commerce platform where multiple sellers ("vendors") can register, list products, manage orders, and receive payments—while customers browse, purchase, and review items. Built using core PHP (no framework) with modern tooling.

---

## 2. Tech Stack

| Layer           | Technology                                     |
|-----------------|-----------------------------------------------|
| Backend         | PHP 8.0+ (Procedural + OOP hybrid)            |
| Database        | MySQL 8.0+                                    |
| Frontend        | HTML5, CSS3, Bootstrap 5                      |
| Package Manager | Composer                                      |
| Server          | Apache (with mod_rewrite) or Nginx            |
| Local Dev       | XAMPP / Laragon / Docker (optional)           |

> **Note**: No JavaScript frameworks (e.g., React/Vue) — vanilla JS + Bootstrap only for simplicity and performance.

---

## 3. Core Features

### 3.1 User Roles
- **Guest**: Browse products, view details.
- **Customer**: Register/login, place orders, track history, write reviews.
- **Vendor**: Register/login, manage store & products, view sales, update order status.
- **Admin**: Manage users, vendors, products, categories, disputes, site settings.

### 3.2 Key Modules
- User Authentication & Authorization
- Vendor Onboarding & Store Profile
- Product Management (CRUD per vendor)
- Category & Subcategory System
- Shopping Cart & Checkout
- Order Management (per vendor + global)
- Basic Reviews & Ratings
- Admin Dashboard

---

## 4. Database Schema (Simplified)

```sql
users (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(100),
  email VARCHAR(150) UNIQUE,
  password VARCHAR(255),
  role ENUM('customer', 'vendor', 'admin') DEFAULT 'customer',
  phone VARCHAR(20),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)

vendor_profiles (
  id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT UNIQUE,
  store_name VARCHAR(100),
  description TEXT,
  address TEXT,
  verified TINYINT(1) DEFAULT 0,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)

categories (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(100) UNIQUE,
  parent_id INT NULL  -- for subcategories
)

products (
  id INT PRIMARY KEY AUTO_INCREMENT,
  vendor_id INT,
  category_id INT,
  name VARCHAR(255),
  description TEXT,
  price DECIMAL(10,2),
  stock INT DEFAULT 0,
  image VARCHAR(255),  -- stores filename
  status ENUM('active','inactive') DEFAULT 'active',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (vendor_id) REFERENCES users(id),
  FOREIGN KEY (category_id) REFERENCES categories(id)
)

cart (
  id INT PRIMARY KEY AUTO_INCREMENT,
  customer_id INT,
  product_id INT,
  quantity INT DEFAULT 1,
  added_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
)

orders (
  id INT PRIMARY KEY AUTO_INCREMENT,
  customer_id INT,
  total_amount DECIMAL(10,2),
  status ENUM('pending','confirmed','shipped','delivered','cancelled') DEFAULT 'pending',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (customer_id) REFERENCES users(id)
)

order_items (
  id INT PRIMARY KEY AUTO_INCREMENT,
  order_id INT,
  product_id INT,
  vendor_id INT,
  quantity INT,
  price_per_unit DECIMAL(10,2),
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id),
  FOREIGN KEY (vendor_id) REFERENCES users(id)
)

reviews (
  id INT PRIMARY KEY AUTO_INCREMENT,
  product_id INT,
  customer_id INT,
  rating TINYINT CHECK (rating BETWEEN 1 AND 5),
  comment TEXT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  FOREIGN KEY (customer_id) REFERENCES users(id)
)