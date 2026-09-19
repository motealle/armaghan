#!/usr/bin/env python3
from pathlib import Path
import sqlite3

ROOT = Path(__file__).resolve().parents[1]
DB = ROOT / "database" / "database.sqlite"
SCHEMA = ROOT / "database" / "schema.sql"

DB.parent.mkdir(parents=True, exist_ok=True)
con = sqlite3.connect(DB)
try:
    con.executescript(SCHEMA.read_text(encoding="utf-8"))
    con.commit()
finally:
    con.close()
print(f"SQLite ready: {DB}")
