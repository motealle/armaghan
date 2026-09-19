PRAGMA foreign_keys = ON;

CREATE TABLE IF NOT EXISTS users (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  email TEXT UNIQUE,
  password_hash TEXT,
  role TEXT NOT NULL CHECK(role IN ('admin','customer')),
  active INTEGER NOT NULL DEFAULT 1,
  created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS customers (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id INTEGER UNIQUE,
  country_code TEXT,
  country_name TEXT,
  whatsapp TEXT,
  company_name TEXT,
  direct_link_enabled INTEGER NOT NULL DEFAULT 1,
  created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS categories (
  id INTEGER PRIMARY KEY,
  code TEXT NOT NULL UNIQUE,
  name_fa TEXT NOT NULL,
  name_ar TEXT,
  name_en TEXT,
  name_ku TEXT
);

CREATE TABLE IF NOT EXISTS subcategories (
  id INTEGER PRIMARY KEY,
  category_id INTEGER NOT NULL,
  code TEXT NOT NULL UNIQUE,
  name_fa TEXT NOT NULL,
  name_ar TEXT,
  name_en TEXT,
  name_ku TEXT,
  FOREIGN KEY(category_id) REFERENCES categories(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS products (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  subcategory_id INTEGER NOT NULL,
  code TEXT NOT NULL UNIQUE,
  name_fa TEXT NOT NULL,
  name_ar TEXT,
  name_en TEXT,
  name_ku TEXT,
  availability TEXT NOT NULL DEFAULT 'available' CHECK(availability IN ('available','unavailable','made_to_order')),
  active INTEGER NOT NULL DEFAULT 1,
  sort_order INTEGER NOT NULL DEFAULT 0,
  created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(subcategory_id) REFERENCES subcategories(id) ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS product_images (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  product_id INTEGER NOT NULL,
  path TEXT NOT NULL,
  alt_fa TEXT,
  width INTEGER,
  height INTEGER,
  bytes INTEGER,
  sort_order INTEGER NOT NULL DEFAULT 0,
  created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS spec_definitions (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  subcategory_id INTEGER NOT NULL,
  key TEXT NOT NULL,
  label_fa TEXT NOT NULL,
  label_ar TEXT,
  label_en TEXT,
  label_ku TEXT,
  locked INTEGER NOT NULL DEFAULT 0,
  sort_order INTEGER NOT NULL DEFAULT 0,
  UNIQUE(subcategory_id,key),
  FOREIGN KEY(subcategory_id) REFERENCES subcategories(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS product_spec_values (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  product_id INTEGER NOT NULL,
  spec_definition_id INTEGER NOT NULL,
  value_text TEXT,
  UNIQUE(product_id,spec_definition_id),
  FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE CASCADE,
  FOREIGN KEY(spec_definition_id) REFERENCES spec_definitions(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS favorites (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  customer_id INTEGER NOT NULL,
  product_id INTEGER NOT NULL,
  created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE(customer_id,product_id),
  FOREIGN KEY(customer_id) REFERENCES customers(id) ON DELETE CASCADE,
  FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS orders (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  customer_id INTEGER NOT NULL,
  public_code TEXT NOT NULL UNIQUE,
  request_type TEXT NOT NULL CHECK(request_type IN ('simple','available','unavailable','custom','brand','packaging')),
  status TEXT NOT NULL DEFAULT 'inquiry',
  prepayment_status TEXT NOT NULL DEFAULT 'not_required',
  notes TEXT,
  created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(customer_id) REFERENCES customers(id) ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS order_items (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  order_id INTEGER NOT NULL,
  product_id INTEGER,
  product_code_snapshot TEXT NOT NULL,
  product_name_snapshot TEXT NOT NULL,
  quantity INTEGER,
  negotiated_specs_json TEXT,
  FOREIGN KEY(order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS order_timeline (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  order_id INTEGER NOT NULL,
  event_key TEXT NOT NULL,
  title TEXT NOT NULL,
  detail TEXT,
  occurred_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  created_by_user_id INTEGER,
  FOREIGN KEY(order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY(created_by_user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS magic_links (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  customer_id INTEGER NOT NULL,
  token_hash TEXT NOT NULL UNIQUE,
  scope TEXT NOT NULL DEFAULT 'customer_portal',
  enabled INTEGER NOT NULL DEFAULT 1,
  expires_at TEXT,
  last_used_at TEXT,
  created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  revoked_at TEXT,
  FOREIGN KEY(customer_id) REFERENCES customers(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS activity_log (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  actor_user_id INTEGER,
  customer_id INTEGER,
  action TEXT NOT NULL,
  subject_type TEXT,
  subject_id INTEGER,
  metadata_json TEXT,
  created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(actor_user_id) REFERENCES users(id) ON DELETE SET NULL,
  FOREIGN KEY(customer_id) REFERENCES customers(id) ON DELETE SET NULL
);

CREATE INDEX IF NOT EXISTS idx_products_subcategory ON products(subcategory_id);
CREATE INDEX IF NOT EXISTS idx_orders_customer ON orders(customer_id);
CREATE INDEX IF NOT EXISTS idx_timeline_order ON order_timeline(order_id);
CREATE INDEX IF NOT EXISTS idx_magic_customer ON magic_links(customer_id);
