# Armaghan — SQLite-first persistence

Status: active production baseline. Laravel/SQLite is live and verified on the hosting environment.

## Ranked architecture

| Rank | Architecture | Score | Decision |
|---:|---|---:|---|
| 1 | **SQLite primary + consistent SQLite snapshots + later MySQL logical mirror** | **10.0** | Selected. Small operational surface, native Laravel/Eloquent support, transactions/indexes, simple private-file backup, easy later migration. |
| 2 | SQLite primary + MySQL as the only backup target | 8.0 | Better than no backup, but backup would depend on a cross-engine conversion path. |
| 3 | MySQL primary + SQLite local/test | 7.9 | Conventional, but operationally heavier than needed for the current scale. |
| 4 | JSON primary + relational security store | 6.0 | No longer justified once PDO SQLite is available on production. |
| 5 | Dual-write SQLite + MySQL on every request | 4.0 | Adds mismatch/retry/transaction complexity without a current business need. |

## Selected persistence model

SQLite becomes Laravel's primary source of truth for:
- users/admins;
- sessions/password reset;
- catalog/category/subcategory/product/spec data;
- customer data;
- favorites-share state;
- magic-link state;
- Style Profile draft/version/publication data;
- audit/activity data;
- future small transactional MVP records.

For the current scale, one database engine is simpler and safer than splitting domain/security data across JSON + MySQL.

## Production configuration

Laravel 13 supports SQLite directly. Production uses:

```dotenv
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/private/path/armaghan/database.sqlite
DB_FOREIGN_KEYS=true
ARMAGHAN_SQLITE_BACKUP_PATH=/absolute/private/path/armaghan/backups
```

The SQLite database and backups must live outside `public_html`.

Production SQLite was re-probed on 2026-10-01 and is now verified **PASS**. The final runtime probe confirmed:
- `pdo_sqlite` is loaded;
- SQLite 3.53.4 is active;
- a private SQLite file can be created/read/written by web PHP;
- foreign keys work;
- `VACUUM INTO` creates a consistent private snapshot;
- the private sibling remains writable.

Do not infer success from the previous 2026-09-30 probe, which was performed before SQLite was enabled.

## Backup policy

### Primary backup

The primary backup format is **SQLite to SQLite**.

Use:

```bash
php artisan armaghan:backup-sqlite
```

The command uses SQLite `VACUUM INTO` through Laravel/PDO to create a consistent snapshot in the configured private backup directory.

Do not make a naive raw copy of a live SQLite file while transactions may be active. SQLite may use journal/WAL sidecar files; use a consistent SQLite backup mechanism instead.

### MySQL role

MySQL/MariaDB is retained as a future **logical mirror/export target**, not the only backup.

A future mirror command should:
1. read a consistent SQLite snapshot or a read transaction;
2. write the same canonical schema/data to a separate configured MySQL connection;
3. record source revision/timestamp;
4. verify row counts/checksums;
5. never participate in the live request transaction.

This avoids dual-write coupling. A failed MySQL mirror must not make the live SQLite write fail.

### Off-host backup

A backup on the same hosting account protects against application/database mistakes, but not full account/storage loss. Before production handoff, keep at least one rotated off-host copy of the SQLite snapshot.

## JSON policy

JSON remains a **plain portable interchange format** only:
- import/export;
- debugging fixture;
- optional customer/catalog export;
- portable settings/profile bundle where useful.

Do not build a custom JSON database engine, locking protocol, query layer, transaction layer or relational emulation.

Use Laravel models/database as the runtime source of truth and ordinary JSON serialization when an export is needed.

## When to outgrow SQLite

Re-evaluate MySQL/PostgreSQL as the primary database if one or more of these become material:
- sustained concurrent write contention;
- background workers generating frequent writes;
- large multi-user admin workload;
- reporting/query needs that benefit from a server database;
- horizontal multi-server application deployment.

The current ~700-record scale alone is not a reason to leave SQLite.

## Sources

- Laravel 13 database documentation: SQLite is a first-party supported database and is configured by an absolute `DB_DATABASE` path.
- SQLite Online Backup documentation: backup API creates a consistent snapshot while allowing concurrent use.
- SQLite `VACUUM INTO` documentation: supported alternative for creating a consistent standalone backup file.


## Production activation result — 2026-10-01

Production Laravel/SQLite is now active on the hosting account.

- Private Laravel application directory: outside `public_html`.
- Shared private state: `.env`, SQLite database and backup snapshots live outside `public_html`.
- Public Laravel surface: `/backend` only.
- Laravel Framework: **13.34.0**.
- Production migrations: **PASS**.
- Initial consistent SQLite snapshot: **PASS**.
- Required schema checks: `users`, `products`, `customers`, `style_profiles`, `style_profile_versions`, `style_profile_publications` all **PASS**.
- HTTP smoke:
  - `/backend/` → 200
  - `/backend/up` → 200
  - `/backend/api/style-profile/staging` → 200
  - `/backend/admin/login` → 200

The first post-deployment HTTP smoke initially returned 404 even though activation, migrations and the initial snapshot had already succeeded. Root cause was public-directory permissions created too restrictively for LiteSpeed. Public Laravel directories/files were normalized to `0755/0644`; private application/data permissions were not widened. The deployment helper now enforces web-safe permissions for the public surface.

A real production active administrator has since been provisioned securely through the separate fail-closed bootstrap/recovery flow. No default password is stored in the repository. The remaining editor acceptance is the real browser Filament session/CSRF/cross-device cycle, not database activation.
