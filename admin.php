<?php
declare(strict_types=1);
$privateDir = dirname(__DIR__) . '/king-choice-private';
if (!is_dir($privateDir)) $privateDir = __DIR__ . '/king-choice-private';
require_once $privateDir . '/core.php';

hkc_send_security_headers(true);
hkc_start_secure_session();

$flashError = '';
$dbConfigured = true;
$pdo = null;

try {
    $pdo = hkc_get_pdo();
} catch (Throwable $e) {
    $dbConfigured = false;
    $flashError = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Portal | Hotel King Choice</title>
    <link rel="stylesheet" href="style.css">
    <style>body { padding: 2rem; background: #FAF6F0; }</style>
</head>
<body>
    <div class="container">
        <h1>Hotel King Choice Admin Portal</h1>
        <?php if (!$dbConfigured): ?>
            <div style="background:#FEF2F2; border-left:4px solid #DC2626; padding:1rem; margin-top:1rem;">
                <strong>Database Connection Notice:</strong> <?= hkc_e($flashError) ?><br>
                Please ensure your database name, user, and password are correctly updated inside <code>king-choice-private/config.php</code>.
            </div>
        <?php else: ?>
            <div style="background:#ECFDF5; border-left:4px solid #059669; padding:1rem; margin-top:1rem;">
                ✅ <strong>Database Connected Successfully!</strong> Your secure backend and MySQL InnoDB tables are fully active for Hotel King Choice.
            </div>
        <?php endif; ?>
        <p style="margin-top:1.5rem;"><a href="index.html" class="btn btn--forest">&larr; Return to Public Website</a></p>
    </div>
</body>
</html>