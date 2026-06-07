<?php
/**
 * RT 10 — Web Deployment Script
 * Access: https://gp2rt10.vensalor-kingdom.com/deploy.php
 * DELETE THIS FILE after deployment is complete.
 */

declare(strict_types=1);

// ─── CHANGE THIS BEFORE UPLOADING ────────────────────────────────────────────
const DEPLOY_PASSWORD = 'rt10deploy2026';
// ─────────────────────────────────────────────────────────────────────────────

// Works whether deploy.php is inside public/ or at the document root
$basePath = file_exists(__DIR__ . '/artisan') ? __DIR__ : realpath(__DIR__ . '/..');
$php      = PHP_BINARY ?: 'php';
$artisan  = $basePath . '/artisan';

// ── Auth ─────────────────────────────────────────────────────────────────────
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

// ── Run command ───────────────────────────────────────────────────────────────
$output = [];
$ran    = false;

if ($authed && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $ran = true;

    $commands = [
        'key_generate'   => [$php, $artisan, 'key:generate', '--force'],
        'migrate'        => [$php, $artisan, 'migrate', '--force'],
        'config_cache'   => [$php, $artisan, 'config:cache'],
        'route_cache'    => [$php, $artisan, 'route:cache'],
        'view_cache'     => [$php, $artisan, 'view:cache'],
        'clear_cache'    => [$php, $artisan, 'optimize:clear'],
        'storage_link'   => [$php, $artisan, 'storage:link'],
        'symlink_uploads'=> null, // handled separately
        'chmod_storage'  => null, // handled separately
    ];

    $action = $_POST['action'];

    if ($action === 'run_all') {
        $steps = ['key_generate', 'migrate', 'config_cache', 'route_cache', 'view_cache', 'symlink_uploads', 'chmod_storage'];
        foreach ($steps as $step) {
            $output[] = runStep($step, $commands, $basePath, $php, $artisan);
        }
    } elseif (isset($commands[$action]) || in_array($action, ['symlink_uploads', 'chmod_storage'])) {
        $output[] = runStep($action, $commands, $basePath, $php, $artisan);
    }
}

function runStep(string $action, array $commands, string $basePath, string $php, string $artisan): array
{
    $result = ['action' => $action, 'lines' => [], 'ok' => true];

    if ($action === 'symlink_uploads') {
        $result['action'] = 'symlink_uploads (storage/uploads → public/uploads)';
        $target = $basePath . '/storage/uploads';
        $link   = $basePath . '/public/uploads';
        if (!is_dir($target)) {
            mkdir($target, 0775, true);
            $result['lines'][] = "Created: storage/uploads/";
        }
        if (is_link($link)) {
            $result['lines'][] = "Symlink already exists: public/uploads → " . readlink($link);
        } elseif (file_exists($link)) {
            $result['lines'][] = "WARNING: public/uploads exists but is not a symlink — skipped.";
            $result['ok'] = false;
        } else {
            if (symlink($target, $link)) {
                $result['lines'][] = "Created symlink: public/uploads → storage/uploads";
            } else {
                $result['lines'][] = "ERROR: Could not create symlink. Create it manually via cPanel File Manager.";
                $result['ok'] = false;
            }
        }
        return $result;
    }

    if ($action === 'chmod_storage') {
        $result['action'] = 'chmod 775 storage/ bootstrap/cache/';
        $dirs = [$basePath . '/storage', $basePath . '/bootstrap/cache'];
        foreach ($dirs as $dir) {
            if (is_dir($dir)) {
                chmodRecursive($dir, 0775, 0664);
                $result['lines'][] = "chmod 775 applied to: " . basename(dirname($dir)) . '/' . basename($dir);
            } else {
                $result['lines'][] = "Missing: $dir";
                $result['ok'] = false;
            }
        }
        return $result;
    }

    if (!isset($commands[$action]) || $commands[$action] === null) {
        $result['lines'][] = "Unknown action: $action";
        $result['ok'] = false;
        return $result;
    }

    $cmd = implode(' ', array_map('escapeshellarg', $commands[$action])) . ' 2>&1';
    exec($cmd, $lines, $code);
    $result['lines'] = $lines ?: ['(no output)'];
    $result['ok']    = ($code === 0);
    return $result;
}

function chmodRecursive(string $path, int $dirMode, int $fileMode): void
{
    chmod($path, $dirMode);
    $items = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($items as $item) {
        chmod((string) $item, $item->isDir() ? $dirMode : $fileMode);
    }
}

// ── Env check ────────────────────────────────────────────────────────────────
$envStatus = [];
if ($authed) {
    $envFile = $basePath . '/.env';
    if (!file_exists($envFile)) {
        $envStatus[] = ['label' => '.env file',    'value' => 'MISSING', 'bad' => true];
    } else {
        $env = parse_ini_file($envFile);
        $envStatus[] = ['label' => 'APP_KEY',      'value' => !empty($env['APP_KEY']) ? '✓ set' : '✗ empty', 'bad' => empty($env['APP_KEY'])];
        $envStatus[] = ['label' => 'APP_ENV',      'value' => $env['APP_ENV'] ?? '?', 'bad' => false];
        $envStatus[] = ['label' => 'APP_DEBUG',    'value' => $env['APP_DEBUG'] ?? '?', 'bad' => ($env['APP_DEBUG'] ?? '') === 'true'];
        $envStatus[] = ['label' => 'DB_DATABASE',  'value' => $env['DB_DATABASE'] ?? '?', 'bad' => false];
        $envStatus[] = ['label' => 'DB_PASSWORD',  'value' => !empty($env['DB_PASSWORD']) && $env['DB_PASSWORD'] !== 'your_password' ? '✓ set' : '✗ placeholder', 'bad' => empty($env['DB_PASSWORD']) || $env['DB_PASSWORD'] === 'your_password'];
        $envStatus[] = ['label' => 'GOOGLE_CLIENT_ID', 'value' => !empty($env['GOOGLE_CLIENT_ID']) && !str_contains($env['GOOGLE_CLIENT_ID'], 'xxx') ? '✓ set' : '✗ placeholder', 'bad' => empty($env['GOOGLE_CLIENT_ID']) || str_contains($env['GOOGLE_CLIENT_ID'], 'xxx')];
    }

    // PHP version
    $envStatus[] = ['label' => 'PHP version', 'value' => PHP_VERSION, 'bad' => version_compare(PHP_VERSION, '8.2.0', '<')];

    // exec() available?
    $execOk = function_exists('exec') && !in_array('exec', array_map('trim', explode(',', ini_get('disable_functions'))));
    $envStatus[] = ['label' => 'exec() enabled', 'value' => $execOk ? '✓ yes' : '✗ disabled', 'bad' => !$execOk];

    // symlink available?
    $slinkOk = function_exists('symlink');
    $envStatus[] = ['label' => 'symlink() enabled', 'value' => $slinkOk ? '✓ yes' : '✗ disabled', 'bad' => !$slinkOk];

    // storage writable?
    $storageOk = is_writable($basePath . '/storage');
    $envStatus[] = ['label' => 'storage/ writable', 'value' => $storageOk ? '✓ yes' : '✗ no', 'bad' => !$storageOk];

    // artisan exists?
    $artisanOk = file_exists($artisan);
    $envStatus[] = ['label' => 'artisan exists', 'value' => $artisanOk ? '✓ yes' : '✗ missing', 'bad' => !$artisanOk];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>RT 10 — Deploy</title>
<style>
  *{box-sizing:border-box;margin:0;padding:0}
  body{font-family:system-ui,sans-serif;background:#0f172a;color:#e2e8f0;min-height:100vh;padding:2rem 1rem}
  .wrap{max-width:800px;margin:0 auto}
  h1{font-size:1.5rem;font-weight:700;color:#f8fafc;margin-bottom:.25rem}
  .sub{color:#94a3b8;font-size:.875rem;margin-bottom:2rem}
  .warn{background:#7c2d12;border:1px solid #ea580c;padding:.75rem 1rem;border-radius:.5rem;font-size:.875rem;margin-bottom:1.5rem;color:#fed7aa}
  .card{background:#1e293b;border:1px solid #334155;border-radius:.75rem;padding:1.5rem;margin-bottom:1.5rem}
  .card h2{font-size:1rem;font-weight:600;color:#cbd5e1;margin-bottom:1rem}
  input[type=password]{width:100%;padding:.625rem .875rem;background:#0f172a;border:1px solid #475569;border-radius:.5rem;color:#f1f5f9;font-size:1rem;margin-bottom:.75rem}
  input[type=password]:focus{outline:none;border-color:#6366f1}
  .btn{display:inline-block;padding:.625rem 1.25rem;border-radius:.5rem;font-size:.875rem;font-weight:600;cursor:pointer;border:none;transition:opacity .15s}
  .btn:hover{opacity:.85}
  .btn-primary{background:#6366f1;color:#fff}
  .btn-green{background:#16a34a;color:#fff}
  .btn-blue{background:#0ea5e9;color:#fff}
  .btn-gray{background:#475569;color:#e2e8f0}
  .btn-red{background:#dc2626;color:#fff}
  .btn-sm{padding:.375rem .75rem;font-size:.8rem}
  .grid{display:grid;grid-template-columns:1fr 1fr;gap:.75rem}
  @media(max-width:500px){.grid{grid-template-columns:1fr}}
  .status-table{width:100%;border-collapse:collapse;font-size:.875rem}
  .status-table td{padding:.5rem .75rem;border-bottom:1px solid #1e293b}
  .status-table tr:last-child td{border-bottom:none}
  .status-table .label{color:#94a3b8;width:40%}
  .ok{color:#4ade80}
  .bad{color:#f87171}
  .output-block{margin-top:1.5rem}
  .output-block h3{font-size:.875rem;font-weight:600;color:#94a3b8;margin-bottom:.5rem;text-transform:uppercase;letter-spacing:.05em}
  .result{background:#0f172a;border:1px solid #334155;border-radius:.5rem;padding:1rem;margin-bottom:.75rem}
  .result .title{font-weight:600;font-size:.9rem;margin-bottom:.5rem}
  .result .title.ok{color:#4ade80}
  .result .title.bad{color:#f87171}
  .result pre{font-family:monospace;font-size:.8rem;color:#94a3b8;white-space:pre-wrap;word-break:break-all}
  .error{color:#f87171;font-size:.875rem;margin-bottom:.75rem}
  .sep{border:none;border-top:1px solid #334155;margin:1rem 0}
</style>
</head>
<body>
<div class="wrap">
  <h1>RT 10 Golden Park 2 — Deployment</h1>
  <p class="sub">gp2rt10.vensalor-kingdom.com &nbsp;|&nbsp; <strong style="color:#fbbf24">DELETE this file after use</strong></p>

  <div class="warn">
    ⚠️ <strong>Security:</strong> This file executes server commands. Delete <code>public/deploy.php</code> immediately after deployment is complete.
  </div>

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

  <!-- Environment Check -->
  <div class="card">
    <h2>Environment Check</h2>
    <table class="status-table">
      <?php foreach ($envStatus as $s): ?>
      <tr>
        <td class="label"><?= htmlspecialchars($s['label']) ?></td>
        <td class="<?= $s['bad'] ? 'bad' : 'ok' ?>"><?= htmlspecialchars($s['value']) ?></td>
      </tr>
      <?php endforeach; ?>
    </table>
  </div>

  <!-- Actions -->
  <div class="card">
    <h2>Run All (Recommended)</h2>
    <p style="color:#94a3b8;font-size:.875rem;margin-bottom:1rem">
      Runs: key:generate → migrate → config:cache → route:cache → view:cache → symlink uploads → chmod storage
    </p>
    <form method="POST">
      <input type="hidden" name="action" value="run_all">
      <button class="btn btn-green" type="submit">▶ Run All Steps</button>
    </form>
  </div>

  <div class="card">
    <h2>Individual Steps</h2>
    <div class="grid">
      <?php
      $steps = [
        ['key_generate',   'key:generate',          'btn-blue'],
        ['migrate',        'migrate --force',        'btn-blue'],
        ['config_cache',   'config:cache',           'btn-gray'],
        ['route_cache',    'route:cache',            'btn-gray'],
        ['view_cache',     'view:cache',             'btn-gray'],
        ['clear_cache',    'optimize:clear',         'btn-gray'],
        ['storage_link',   'storage:link',           'btn-gray'],
        ['symlink_uploads','Symlink uploads folder', 'btn-gray'],
        ['chmod_storage',  'chmod 775 storage/',     'btn-gray'],
      ];
      foreach ($steps as [$action, $label, $cls]):
      ?>
      <form method="POST">
        <input type="hidden" name="action" value="<?= $action ?>">
        <button class="btn <?= $cls ?> btn-sm" type="submit" style="width:100%"><?= htmlspecialchars($label) ?></button>
      </form>
      <?php endforeach; ?>
    </div>

    <hr class="sep">

    <form method="POST" style="margin-top:.5rem">
      <input type="hidden" name="logout" value="1">
      <button class="btn btn-red btn-sm" type="submit">Logout</button>
    </form>
  </div>

  <!-- Output -->
  <?php if ($ran && !empty($output)): ?>
  <div class="output-block">
    <h3>Output</h3>
    <?php foreach ($output as $result): ?>
    <div class="result">
      <div class="title <?= $result['ok'] ? 'ok' : 'bad' ?>">
        <?= $result['ok'] ? '✓' : '✗' ?> <?= htmlspecialchars($result['action']) ?>
      </div>
      <pre><?= htmlspecialchars(implode("\n", $result['lines'])) ?></pre>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

<?php endif; ?>
</div>
</body>
</html>
