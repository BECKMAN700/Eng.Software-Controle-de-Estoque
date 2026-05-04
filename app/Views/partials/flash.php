<?php
// Mensagens flash via sessão (prioridade)
$flashSucesso = Sessao::getFlashSucesso();
$flashErro    = Sessao::getFlashErro();
?>

<?php if ($flashSucesso !== ''): ?>
    <div class="alert alert-success" role="alert">
        <?= htmlspecialchars($flashSucesso) ?>
    </div>
<?php endif; ?>

<?php if ($flashErro !== ''): ?>
    <div class="alert alert-danger" role="alert">
        <?= htmlspecialchars($flashErro) ?>
    </div>
<?php endif; ?>

<?php if (!empty($mensagemSucesso)): ?>
    <div class="alert alert-success" role="alert">
        <?= htmlspecialchars($mensagemSucesso) ?>
    </div>
<?php endif; ?>

<?php if (!empty($mensagemErro)): ?>
    <div class="alert alert-danger" role="alert">
        <?= htmlspecialchars($mensagemErro) ?>
    </div>
<?php endif; ?>