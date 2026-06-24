<?php
// Mensagens flash via sessão (prioridade)
$flashSucesso = Sessao::getFlashSucesso();
$flashErro    = Sessao::getFlashErro();

// data-flash-toast: o ui.js transforma em toast flutuante.
// Sem JavaScript, o alerta permanece visível no topo (degradação graciosa).
?>

<?php if ($flashSucesso !== ''): ?>
    <div class="alert alert-success" role="status" data-flash-toast="success">
        <?= htmlspecialchars($flashSucesso) ?>
    </div>
<?php endif; ?>

<?php if ($flashErro !== ''): ?>
    <div class="alert alert-danger" role="alert" data-flash-toast="danger">
        <?= htmlspecialchars($flashErro) ?>
    </div>
<?php endif; ?>

<?php if (!empty($mensagemSucesso)): ?>
    <div class="alert alert-success" role="status" data-flash-toast="success">
        <?= htmlspecialchars($mensagemSucesso) ?>
    </div>
<?php endif; ?>

<?php if (!empty($mensagemErro)): ?>
    <div class="alert alert-danger" role="alert" data-flash-toast="danger">
        <?= htmlspecialchars($mensagemErro) ?>
    </div>
<?php endif; ?>
