"""Apply database/schema.sql using the ignored local .env connection settings."""

import argparse
import ssl
from pathlib import Path

import pymysql


ROOT = Path(__file__).resolve().parents[1]


def load_env(path):
    values = {}
    for raw_line in path.read_text(encoding="utf-8").splitlines():
        line = raw_line.strip()
        if not line or line.startswith("#") or "=" not in line:
            continue
        key, value = line.split("=", 1)
        values[key] = value.strip().strip('"')
    return values


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument("--verify-only", action="store_true")
    args = parser.parse_args()
    env = load_env(ROOT / ".env")
    tls = ssl.create_default_context(cafile=str(ROOT / env["DB_SSL_CA"]))
    connection = pymysql.connect(
        host=env["DB_HOST"],
        port=int(env["DB_PORT"]),
        user=env["DB_USERNAME"],
        password=env["DB_PASSWORD"],
        database=env["DB_DATABASE"],
        ssl=tls,
        autocommit=False,
        connect_timeout=15,
    )

    try:
        with connection.cursor() as cursor:
            if not args.verify_only:
                for statement in (ROOT / "database" / "schema.sql").read_text(encoding="utf-8").split(";"):
                    statement = statement.strip()
                    if statement:
                        cursor.execute(statement)
                connection.commit()

            cursor.execute(
                "SELECT TABLE_NAME FROM information_schema.TABLES "
                "WHERE TABLE_SCHEMA=%s AND TABLE_NAME IN ('users', 'products') "
                "ORDER BY TABLE_NAME",
                (env["DB_DATABASE"],),
            )
            tables = [row[0] for row in cursor.fetchall()]
            cursor.execute("SELECT COUNT(*) FROM users")
            users = cursor.fetchone()[0]
            cursor.execute("SELECT COUNT(*) FROM products")
            products = cursor.fetchone()[0]
            cipher = connection._sock.cipher()[0]
        print({"database": env["DB_DATABASE"], "tls": cipher, "tables": tables, "users": users, "products": products})
    finally:
        connection.close()


if __name__ == "__main__":
    main()
