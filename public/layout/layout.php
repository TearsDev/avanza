<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo ?? 'AVANZA') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Quicksand:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/tokens.css">
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/layout.css">
    <link rel="stylesheet" href="assets/css/components.css">
    <?php if (!empty($css_page) && preg_match('/^[a-z0-9_-]+$/i', $css_page)): ?>
    <link rel="stylesheet" href="assets/css/pages/<?= htmlspecialchars($css_page) ?>.css">
    <?php endif; ?>
</head>
<body>
    <div class="app">
        <?php include __DIR__ . '/nav.php'; ?>
        <main>
            <?php if (!empty($contenido)) include $contenido; ?>
        </main>
        <?php include __DIR__ . '/footer.php'; ?>
    </div>
</body>
</html>
