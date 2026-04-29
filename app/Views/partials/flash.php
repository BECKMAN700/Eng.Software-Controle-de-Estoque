<?php if (!empty($mensagemSucesso)): ?>
    <div class="alert alert-success">
        <?= htmlspecialchars($mensagemSucesso) ?>
    </div>
<?php endif; ?>

<?php if (!empty($mensagemErro)): ?>
    <div class="alert alert-danger">
        <?= htmlspecialchars($mensagemErro) ?>
    </div>
<?php endif; ?>

<?php if (!empty($_GET['msg'])): ?>
    <div class="alert alert-success">
        <?= htmlspecialchars($_GET['msg']) ?>
    </div>
<?php endif; ?>

<?php if (!empty($_GET['erro'])): ?>
    <div class="alert alert-danger">
        <?= htmlspecialchars($_GET['erro']) ?>
    </div>
<?php endif; ?>