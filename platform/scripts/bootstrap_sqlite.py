#!/usr/bin/env python3
from pathlib import Path
import sqlite3

ROOT = Path(__file__).resolve().parents[1]
DB = ROOT / "database" / "database.sqlite"
SCHEMA = ROOT / "database" / "schema.sql"
SEED = ROOT / "database" / "seed_demo.sql"

DB.parent.mkdir(parents=True, exist_ok=True)
con = sqlite3.connect(DB)
try:
    con.executescript(SCHEMA.read_text(encoding="utf-8"))
    con.executescript(SEED.read_text(encoding="utf-8"))
    con.commit()
    product_count = con.execute("SELECT COUNT(*) FROM products").fetchone()[0]
    customer_count = con.execute("SELECT COUNT(*) FROM customers").fetchone()[0]
finally:
    con.close()
print(f"SQLite ready: {DB} ({product_count} products, {customer_count} customers)")
