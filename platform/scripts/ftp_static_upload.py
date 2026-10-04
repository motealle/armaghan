"""Bounded recovery for idempotent static-file replacement, never application POSTs."""
from ftplib import error_temp


def upload_static_file(ftp, connect, remote_dir, filename, local_path, attempts=3):
    for attempt in range(attempts):
        try:
            # Reopen from byte zero: a prior timeout may have written only a prefix.
            ftp.cwd(remote_dir)
            with open(local_path, "rb") as source:
                ftp.storbinary(f"STOR {filename}", source)
            return ftp
        except (OSError, EOFError, error_temp):
            ftp.close()
            if attempt + 1 == attempts:
                raise
            ftp = connect()
    raise RuntimeError("Static upload attempts exhausted")
