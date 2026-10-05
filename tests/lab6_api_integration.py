"""Exercise the real PHP HTTP API against an isolated SQLite database.

Usage: python tests/lab6_api_integration.py --php /path/to/php
The existing Aiven database and .env are never modified.
"""
import argparse
import json
import os
import secrets
from pathlib import Path
import shutil
import socket
import sqlite3
import subprocess
import tempfile
import time
from urllib import request, error

ROOT = Path(__file__).resolve().parents[1]


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument('--php', required=True)
    args = parser.parse_args()
    with tempfile.TemporaryDirectory(prefix='lab6-', dir=ROOT / 'tmp') as temp:
        folder = Path(temp)
        for name in ['app', 'scheme', 'public', 'runtime', 'scripts']:
            shutil.copytree(ROOT / name, folder / name,
                            ignore=shutil.ignore_patterns('*.log', '*.cache', 'session_security.json'))
        database = folder / 'fixture.sqlite'
        conn = sqlite3.connect(database)
        conn.executescript('''
          CREATE TABLE users (id INTEGER PRIMARY KEY, username TEXT, email TEXT,
                              password TEXT, is_active INTEGER DEFAULT 1, role TEXT DEFAULT 'admin');
          CREATE TABLE refresh_tokens (id INTEGER PRIMARY KEY AUTOINCREMENT,
                       user_id INTEGER, token TEXT, expires_at TEXT, jti TEXT);
          CREATE TABLE products (id INTEGER PRIMARY KEY AUTOINCREMENT,
                       product_name TEXT, description TEXT, price NUMERIC, quantity INTEGER,
                       created_at TEXT DEFAULT CURRENT_TIMESTAMP);
        ''')
        hashed = subprocess.check_output([args.php, '-r',
            'echo password_hash("Lab6-fixture-only!", PASSWORD_DEFAULT);'], text=True)
        conn.execute('INSERT INTO users (id, username, email, password, is_active) VALUES (?, ?, ?, ?, ?)',
                     (1, 'fixture_admin', 'fixture@example.test', hashed, 1))
        conn.commit()
        env = os.environ.copy()
        env.update(APP_KEY='fixture-only-' + 'a' * 48, JWT_SECRET=secrets.token_hex(32),
                   REFRESH_TOKEN_KEY=secrets.token_hex(32), DB_DRIVER='sqlite',
                   DB_SQLITE_PATH=str(database), APP_ENV='testing', FRONTEND_URL='http://localhost:5173')
        with socket.socket() as sock:
            sock.bind(('127.0.0.1', 0))
            port = sock.getsockname()[1]
        base = f'http://127.0.0.1:{port}'
        with (folder / 'server.log').open('w') as log:
            server = subprocess.Popen([args.php, '-d', 'extension=pdo_sqlite', '-S',
                f'127.0.0.1:{port}', '-t', 'public', 'scripts/dev-router.php'],
                cwd=folder, env=env, stdout=log, stderr=log)
            try:
                def call(path, method='GET', data=None, token=None, expected=200, raw=None, content_type='application/json'):
                    headers = {'Origin': 'http://localhost:5173'}
                    body = raw if raw is not None else (json.dumps(data).encode() if data is not None else None)
                    if body is not None: headers['Content-Type'] = content_type
                    if token: headers['Authorization'] = 'Bearer ' + token
                    req = request.Request(base + path, data=body, headers=headers, method=method)
                    try: response = request.urlopen(req, timeout=10)
                    except error.HTTPError as exc: response = exc
                    payload = response.read().decode()
                    assert response.status == expected, (path, method, response.status, payload[:200])
                    if method == 'OPTIONS':
                        assert response.headers['Access-Control-Allow-Origin'] == headers['Origin']
                        return {}
                    return json.loads(payload)
                for _ in range(40):
                    try: call('/api/health'); break
                    except (error.URLError, OSError): time.sleep(.15)
                else: raise AssertionError('PHP server did not start')
                for method in ['GET', 'POST', 'PUT', 'PATCH', 'DELETE']:
                    path = '/api/products' if method in ['GET', 'POST'] else '/api/products/1'
                    call(path, method, {} if method != 'GET' else None, expected=401)
                call('/api/products/1', 'OPTIONS', expected=204)
                call('/api/login', 'POST', {'username': 'fixture_admin', 'password': 'wrong'}, expected=401)
                auth = call('/api/login', 'POST', {'username': 'fixture_admin', 'password': 'Lab6-fixture-only!'})
                access, refresh = auth['tokens']['access_token'], auth['tokens']['refresh_token']
                assert 'password' not in auth['user']
                call('/api/products', token=refresh, expected=401)
                call('/api/me', token=access)
                call('/api/products', 'POST', {}, access, expected=422)
                call('/api/products', 'POST', raw=b'{bad', token=access, expected=400)
                item = {'product_name': 'Nike "Test" <Pair>', 'description': 'Fixture product', 'price': '5995.50', 'quantity': '8'}
                created = call('/api/products', 'POST', item, access, expected=201)['data']
                pid = created['id']
                assert created['product_name'] == item['product_name']
                assert len(call('/api/products', token=access)['data']) == 1
                item['quantity'] = '12'
                assert call(f'/api/products/{pid}', 'PUT', item, access)['data']['quantity'] == 12
                assert call(f'/api/products/{pid}', 'PATCH', {'quantity': '0'}, access)['data']['quantity'] == 0
                call(f'/api/products/{pid}', 'PATCH', {'quantity': '1.5'}, access, expected=422)
                call(f'/api/products/{pid}', 'PATCH', {'price': '100000000'}, access, expected=422)
                call(f'/api/products/{pid}', 'PATCH', {}, access, expected=422)
                call('/api/products/99999', token=access, expected=404)
                stored = conn.execute('SELECT token FROM refresh_tokens').fetchone()[0]
                assert stored != refresh
                rotated = call('/api/refresh', 'POST', {'refresh_token': refresh})
                call('/api/me', token=access, expected=401)
                call('/api/refresh', 'POST', {'refresh_token': refresh}, expected=401)
                access = rotated['tokens']['access_token']
                refresh = rotated['tokens']['refresh_token']
                call(f'/api/products/{pid}', 'DELETE', token=access)
                call(f'/api/products/{pid}', token=access, expected=404)
                assert call('/api/products', token=access)['data'] == []
                call('/api/logout', 'POST', {'refresh_token': refresh})
                call('/api/products', token=access, expected=401)
                call('/api/refresh', 'POST', {'refresh_token': refresh}, expected=401)
                print('PASS: protected GET/POST/PUT/PATCH/DELETE, login, input validation, CORS, refresh rotation, logout revocation.')
            finally:
                server.terminate()
                server.wait(timeout=10)
                conn.close()


if __name__ == '__main__':
    main()
