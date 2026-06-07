<?php
/**
 * RT 10 — Web Deployment Script
 * Access: https://gp2rt10.vensalor-kingdom.com/deploy.php
 * DELETE THIS FILE after deployment is complete.
 */

declare(strict_types=1);

const DEPLOY_PASSWORD = 'rt10deploy2026';

$basePath = file_exists(__DIR__ . '/artisan') ? __DIR__ : realpath(__DIR__ . '/..');

session_start();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    if ($_POST['password'] === DEPLOY_PASSWORD) {
        $_SESSION['deploy_auth'] = true;
    } else {
        $error = 'Wrong password.';
    }
}
if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

$authed = $_SESSION['deploy_auth'] ?? false;

// ── Run artisan via Laravel bootstrap (no exec needed) ────────────────────────
function runArtisan(string $basePath, string $command, array $args = []): array
{
    ob_start();
    try {
        require_once $basePath . '/vendor/autoload.php';
        $app    = require $basePath . '/bootstrap/app.php';
        $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
        $status = $kernel->call($command, $args);
        $out    = $kernel->output();
        $app->terminate(request(), response());
    } catch (Throwable $e) {
        $out    = get_class($e) . ': ' . $e->getMessage() . "\n" . $e->getFile() . ':' . $e->getLine();
        $status = 1;
    }
    ob_end_clean();
    return ['ok' => $status === 0, 'output' => trim($out) ?: '(no output)'];
}

function chmodRecursive(string $path, int $dirMode, int $fileMode): void
{
    chmod($path, $dirMode);
    $items = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($items as $item) {
        chmod((string)$item, $item->isDir() ? $dirMode : $fileMode);
    }
}

$output = [];
$ran    = false;

if ($authed && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $ran    = true;
    $action = $_POST['action'];

    $steps = [
        'migrate'        => fn() => runArtisan($basePath, 'migrate', ['--force' => true]),
        'migrate_fresh'  => fn() => runArtisan($basePath, 'migrate:fresh', ['--force' => true]),
        'config_cache' => fn() => runArtisan($basePath, 'config:cache'),
        'route_cache'  => fn() => runArtisan($basePath, 'route:cache'),
        'view_cache'   => fn() => runArtisan($basePath, 'view:cache'),
        'clear_cache'  => fn() => runArtisan($basePath, 'optimize:clear'),
        'chmod_storage'=> function () use ($basePath) {
            $dirs = [$basePath . '/storage', $basePath . '/bootstrap/cache'];
            $lines = [];
            $ok = true;
            foreach ($dirs as $dir) {
                if (is_dir($dir)) {
                    chmodRecursive($dir, 0775, 0664);
                    $lines[] = 'chmod 775 applied: ' . basename($dir);
                } else {
                    $lines[] = 'Missing: ' . $dir;
                    $ok = false;
                }
            }
            return ['ok' => $ok, 'output' => implode("\n", $lines)];
        },
        'make_uploads' => function () use ($basePath) {
            $dir = $basePath . '/storage/uploads';
            if (!is_dir($dir) && !mkdir($dir, 0775, true)) {
                return ['ok' => false, 'output' => 'Failed to create storage/uploads/'];
            }
            chmod($dir, 0775);
            return ['ok' => true, 'output' => 'storage/uploads/ ready'];
        },
    ];

    $run = function (string $name) use ($steps, &$output) {
        $label = str_replace('_', ' ', $name);
        if (!isset($steps[$name])) {
            $output[] = ['label' => $label, 'ok' => false, 'output' => 'Unknown step'];
            return;
        }
        $r = ($steps[$name])();
        $output[] = ['label' => $label, 'ok' => $r['ok'], 'output' => $r['output']];
    };

    if ($action === 'run_all') {
        foreach (array_keys($steps) as $name) $run($name);
    } elseif (isset($steps[$action])) {
        $run($action);
    }
}

// ── Env check ────────────────────────────────────────────────────────────────
$envStatus = [];
if ($authed) {
    $envFile = $basePath . '/.env';
    if (!file_exists($envFile)) {
        $envStatus[] = ['label' => '.env file', 'value' => 'MISSING', 'bad' => true];
    } else {
        $env = parse_ini_file($envFile, false, INI_SCANNER_RAW);
        $checks = [
            ['APP_KEY',     !empty($env['APP_KEY'])     ? '✓ set' : '✗ empty',       empty($env['APP_KEY'])],
            ['APP_ENV',     $env['APP_ENV']     ?? '?',                                false],
            ['APP_DEBUG',   $env['APP_DEBUG']   ?? '?',                                ($env['APP_DEBUG'] ?? '') === 'true'],
            ['DB_DATABASE', $env['DB_DATABASE'] ?? '?',                                false],
            ['DB_PASSWORD', !empty($env['DB_PASSWORD']) && $env['DB_PASSWORD'] !== 'your_password' ? '✓ set' : '✗ placeholder', empty($env['DB_PASSWORD']) || $env['DB_PASSWORD'] === 'your_password'],
            ['GOOGLE_CLIENT_ID', !empty($env['GOOGLE_CLIENT_ID']) && !str_contains($env['GOOGLE_CLIENT_ID'], 'xxx') ? '✓ set' : '✗ placeholder', str_contains($env['GOOGLE_CLIENT_ID'] ?? '', 'xxx')],
        ];
        foreach ($checks as [$l, $v, $b]) {
            $envStatus[] = ['label' => $l, 'value' => $v, 'bad' => $b];
        }
    }
    $envStatus[] = ['label' => 'PHP version',       'value' => PHP_VERSION,                                            'bad' => version_compare(PHP_VERSION, '8.2.0', '<')];
    $envStatus[] = ['label' => 'exec() enabled',    'value' => function_exists('exec') ? '✓ yes' : '✗ disabled (OK — not needed)', 'bad' => false];
    $envStatus[] = ['label' => 'storage/ writable', 'value' => is_writable($basePath . '/storage') ? '✓ yes' : '✗ no', 'bad' => !is_writable($basePath . '/storage')];
    $envStatus[] = ['label' => 'artisan exists',    'value' => file_exists($basePath . '/artisan') ? '✓ yes' : '✗ missing', 'bad' => !file_exists($basePath . '/artisan')];
    $envStatus[] = ['label' => 'vendor exists',     'value' => file_exists($basePath . '/vendor/autoload.php') ? '✓ yes' : '✗ missing', 'bad' => !file_exists($basePath . '/vendor/autoload.php')];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>RT 10 — Deploy</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:system-ui,sans-serif;background:#0f172a;color:#e2e8f0;min-height:100vh;padding:2rem 1rem}
.wrap{max-width:720px;margin:0 auto}
h1{font-size:1.4rem;font-weight:700;color:#f8fafc;margin-bottom:.2rem}
.sub{color:#94a3b8;font-size:.85rem;margin-bottom:1.5rem}
.warn{background:#7c2d12;border:1px solid #ea580c;padding:.7rem 1rem;border-radius:.5rem;font-size:.85rem;margin-bottom:1.2rem;color:#fed7aa}
.card{background:#1e293b;border:1px solid #334155;border-radius:.75rem;padding:1.25rem;margin-bottom:1.2rem}
.card h2{font-size:.95rem;font-weight:600;color:#cbd5e1;margin-bottom:.9rem}
input[type=password]{width:100%;padding:.6rem .85rem;background:#0f172a;border:1px solid #475569;border-radius:.5rem;color:#f1f5f9;font-size:1rem;margin-bottom:.7rem}
input[type=password]:focus{outline:none;border-color:#6366f1}
.btn{display:inline-block;padding:.55rem 1.1rem;border-radius:.45rem;font-size:.85rem;font-weight:600;cursor:pointer;border:none}
.btn:hover{opacity:.85}
.btn-primary{background:#6366f1;color:#fff}
.btn-green{background:#16a34a;color:#fff}
.btn-blue{background:#0ea5e9;color:#fff}
.btn-gray{background:#475569;color:#e2e8f0}
.btn-red{background:#dc2626;color:#fff}
.btn-sm{padding:.35rem .7rem;font-size:.8rem}
.grid{display:grid;grid-template-columns:1fr 1fr;gap:.6rem}
@media(max-width:480px){.grid{grid-template-columns:1fr}}
table{width:100%;border-collapse:collapse;font-size:.85rem}
td{padding:.45rem .7rem;border-bottom:1px solid #1e293b}
tr:last-child td{border-bottom:none}
.lbl{color:#94a3b8;width:42%}
.ok{color:#4ade80}.bad{color:#f87171}
.result{background:#0f172a;border:1px solid #334155;border-radius:.5rem;padding:.85rem;margin-bottom:.6rem}
.rtitle{font-weight:600;font-size:.88rem;margin-bottom:.4rem}
.rtitle.ok{color:#4ade80}.rtitle.bad{color:#f87171}
pre{font-family:monospace;font-size:.78rem;color:#94a3b8;white-space:pre-wrap;word-break:break-all}
.error{color:#f87171;font-size:.85rem;margin-bottom:.7rem}
hr{border:none;border-top:1px solid #334155;margin:.9rem 0}
</style>
</head>
<body>
<div class="wrap">
  <h1>RT 10 Golden Park 2 — Deployment</h1>
  <p class="sub">gp2rt10.vensalor-kingdom.com &nbsp;|&nbsp; <strong style="color:#fbbf24">DELETE this file after use</strong></p>
  <div class="warn">⚠️ Delete <code>deploy.php</code> from the server immediately after deployment.</div>

<?php if (!$authed): ?>
  <div class="card">
    <h2>Login</h2>
    <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <form method="POST">
      <input type="password" name="password" placeholder="Deploy password" autofocus>
      <button class="btn btn-primary" type="submit">Masuk</button>
    </form>
  </div>
<?php else: ?>

  <div class="card">
    <h2>Environment</h2>
    <table>
      <?php foreach ($envStatus as $s): ?>
      <tr><td class="lbl"><?= htmlspecialchars($s['label']) ?></td>
          <td class="<?= $s['bad'] ? 'bad' : 'ok' ?>"><?= htmlspecialchars($s['value']) ?></td></tr>
      <?php endforeach; ?>
    </table>
  </div>

  <div class="card">
    <h2>Run All Steps</h2>
    <p style="color:#94a3b8;font-size:.85rem;margin-bottom:.9rem">migrate → config:cache → route:cache → view:cache → chmod storage → create uploads folder</p>
    <form method="POST">
      <input type="hidden" name="action" value="run_all">
      <button class="btn btn-green" type="submit">▶ Run All Steps</button>
    </form>
  </div>

  <div class="card">
    <h2>Individual Steps</h2>
    <div class="grid">
      <?php foreach ([
        ['migrate_fresh', '⚠ Migrate Fresh (drops all data)', 'btn-red'],
        ['migrate',       'migrate --force',       'btn-blue'],
        ['config_cache',  'config:cache',           'btn-gray'],
        ['route_cache',   'route:cache',            'btn-gray'],
        ['view_cache',    'view:cache',             'btn-gray'],
        ['clear_cache',   'optimize:clear',         'btn-gray'],
        ['chmod_storage', 'chmod 775 storage/',     'btn-gray'],
        ['make_uploads',  'Create uploads folder',  'btn-gray'],
      ] as [$action, $label, $cls]): ?>
      <form method="POST">
        <input type="hidden" name="action" value="<?= $action ?>">
        <button class="btn <?= $cls ?> btn-sm" style="width:100%" type="submit"><?= htmlspecialchars($label) ?></button>
      </form>
      <?php endforeach; ?>
    </div>
    <hr>
    <form method="POST"><input type="hidden" name="logout" value="1">
      <button class="btn btn-red btn-sm" type="submit">Logout</button>
    </form>
  </div>

  <?php if ($ran && !empty($output)): ?>
  <div class="card">
    <h2>Output</h2>
    <?php foreach ($output as $r): ?>
    <div class="result">
      <div class="rtitle <?= $r['ok'] ? 'ok' : 'bad' ?>"><?= $r['ok'] ? '✓' : '✗' ?> <?= htmlspecialchars($r['label']) ?></div>
      <pre><?= htmlspecialchars($r['output']) ?></pre>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

<?php endif; ?>
</div>
</body>
</html>
