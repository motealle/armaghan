#!/usr/bin/env python3
from pathlib import Path
import sqlite3

root = Path(__file__).resolve().parents[1]
schema = (root / "platform" / "database" / "schema.sql").read_text(encoding="utf-8")
seed = (root / "platform" / "database" / "seed_demo.sql").read_text(encoding="utf-8")
con = sqlite3.connect(":memory:")
con.executescript(schema)
con.executescript(seed)
tables = {row[0] for row in con.execute("SELECT name FROM sqlite_master WHERE type='table'")}
required = {
    "users","customers","categories","subcategories","products","product_images",
    "spec_definitions","product_spec_values","favorites","orders","order_items",
    "order_timeline","magic_links","activity_log"
}
missing = required - tables
assert not missing, f"Missing SQLite tables: {sorted(missing)}"
assert con.execute("PRAGMA foreign_keys").fetchone()[0] == 1
assert con.execute("SELECT COUNT(*) FROM categories").fetchone()[0] == 3
assert con.execute("SELECT COUNT(*) FROM subcategories").fetchone()[0] == 6
assert con.execute("SELECT COUNT(*) FROM products").fetchone()[0] >= 18
assert con.execute("SELECT COUNT(*) FROM product_images").fetchone()[0] >= 18
assert con.execute("SELECT COUNT(*) FROM customers").fetchone()[0] >= 6
assert con.execute("SELECT COUNT(*) FROM orders").fetchone()[0] >= 4
assert con.execute("SELECT COUNT(*) FROM order_timeline").fetchone()[0] >= 6
print("SQLite schema + demo seed smoke test: PASS")
