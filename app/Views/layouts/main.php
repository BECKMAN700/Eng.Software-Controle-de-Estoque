<?php
$pageTitle = $pageTitle ?? 'Controle de Estoque';
$pageSubtitle = $pageSubtitle ?? 'Gerencie produtos, entradas, saídas e alertas de estoque.';
$content = $content ?? '';
$currentAction = $_GET['acao'] ?? 'listar';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | Controle de Estoque</title>

    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/layout.css">
    <link rel="stylesheet" href="assets/css/components.css">
    <link rel="stylesheet" href="assets/css/pages.css">
</head>
<body>
    <div class="app-shell">
        <?php require __DIR__ . '/../partials/sidebar.php'; ?>

        <div class="app-main">
            <?php require __DIR__ . '/../partials/topbar.php'; ?>

            <main class="content-area">
                <?php require __DIR__ . '/../partials/flash.php'; ?>

                <?= $content ?>
            </main>
        </div>
    </div>
</body>
</html>