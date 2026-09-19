#!/usr/bin/env python3
from pathlib import Path
import sqlite3

schema = (Path(__file__).resolve().parents[1] / "platform" / "database" / "schema.sql").read_text(encoding="utf-8")
con = sqlite3.connect(":memory:")
con.executescript(schema)
tables = {row[0] for row in con.execute("SELECT name FROM sqlite_master WHERE type='table'")}
required = {
    "users","customers","categories","subcategories","products","product_images",
    "spec_definitions","product_spec_values","favorites","orders","order_items",
    "order_timeline","magic_links","activity_log"
}
missing = required - tables
assert not missing, f"Missing SQLite tables: {sorted(missing)}"
fk = con.execute("PRAGMA foreign_keys").fetchone()[0]
assert fk == 1, "Foreign keys must be enabled by schema"
print("SQLite schema smoke test: PASS")
