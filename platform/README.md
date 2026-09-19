# Armaghan Platform Foundation

This directory is the production-application track. It is intentionally separate from the deployed static UX snapshots in `/t`.

## Database now
Development starts on **SQLite** and needs no DB server credentials.

Bootstrap locally:

```bash
python platform/scripts/bootstrap_sqlite.py
```

This creates `platform/database/database.sqlite` from `platform/database/schema.sql`.

The generated SQLite file is local state and should not be committed.

## Laravel target
Next backend milestone:
- Laravel
- Inertia + Vue 3 + Tailwind customer app
- Filament admin
- Socialite for Google login
- secure session auth + password reset
- hashed/revocable magic links
- auditable admin impersonation
- media pipeline with WebP/AVIF responsive variants

The SQL file is a model draft and test oracle until it is converted to Laravel migrations.

## MySQL later
MySQL credentials are deliberately not needed now. At cutover, set DB variables only through deployment environment/secrets and run the same migrations on staging before production.
