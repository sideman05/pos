-- Basic schema for POS
CREATE TABLE products (id INTEGER PRIMARY KEY, name TEXT, price REAL, stock INTEGER);
CREATE TABLE sales (id INTEGER PRIMARY KEY, created_at TEXT, total REAL);
CREATE TABLE sale_items (id INTEGER PRIMARY KEY, sale_id INTEGER, product_id INTEGER, qty INTEGER, price REAL);
