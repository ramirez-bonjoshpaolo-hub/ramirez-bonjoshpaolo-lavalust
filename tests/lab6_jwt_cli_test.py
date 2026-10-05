"""Test the supplied JWT generator on a disposable .env, never the real one."""
import argparse
from pathlib import Path
import re
import shutil
import subprocess
import tempfile

parser = argparse.ArgumentParser()
parser.add_argument('--php', required=True)
args = parser.parse_args()
root = Path(__file__).resolve().parents[1]
with tempfile.TemporaryDirectory(prefix='jwt-cli-', dir=root / 'tmp') as temp:
    folder = Path(temp)
    (folder / 'console').mkdir()
    shutil.copyfile(root / 'console/cli.php', folder / 'console/cli.php')
    shutil.copyfile(root / 'lava', folder / 'lava')
    original = '# Fixture only\nAPP_KEY=preserve-this-fixture\nJWT_SECRET=""\nREFRESH_TOKEN_KEY=\n'
    (folder / '.env').write_text(original)

    def generate(*flags):
        result = subprocess.run([args.php, 'lava', 'jwt:generate', *flags], cwd=folder,
                                capture_output=True, text=True, check=True)
        content = (folder / '.env').read_text()
        values = dict(re.findall(r'^(JWT_SECRET|REFRESH_TOKEN_KEY)=([0-9a-f]{64})$', content, re.M))
        assert len(values) == 2
        assert values['JWT_SECRET'] != values['REFRESH_TOKEN_KEY']
        assert all(len(set(value)) >= 10 for value in values.values())
        assert all(value not in result.stdout + result.stderr for value in values.values())
        assert 'APP_KEY=preserve-this-fixture' in content
        return values, content

    first, content = generate()
    second, preserved = generate()
    assert first == second and content == preserved
    rotated, _ = generate('--force')
    assert all(rotated[name] != first[name] for name in first)
print('PASS: JWT CLI generates independent private keys, preserves existing values, and rotates only with --force.')
