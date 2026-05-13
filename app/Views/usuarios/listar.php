<?php
$pageTitle = 'Usuarios';
$pageSubtitle = 'Gerencie os usuarios que podem acessar o sistema.';

if (!function_exists('esc')) {
    function esc($valor): string
    {
        return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
    }
}

ob_start();
?>

<section class="page-section">
    <div class="card">
        <div class="card-header">
            <div>
                <h2>Usuarios do sistema</h2>
                <p>Consulte os usuarios cadastrados e seus respectivos papeis.</p>
            </div>

            <a href="index.php?acao=usuario_criar" class="btn btn-primary">
                Cadastrar usuario
            </a>
        </div>

        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Papel</th>
                        <th>Status</th>
                        <th>Criado em</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($usuarios)): ?>
                        <tr>
                            <td colspan="5">Nenhum usuario cadastrado.</td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?= esc($usuario['nome'] ?? '') ?></td>
                            <td><?= esc($usuario['email'] ?? '') ?></td>
                            <td><?= esc(($usuario['papel'] ?? '') === 'admin' ? 'Administrador' : 'Estoquista') ?></td>
                            <td><?= esc(($usuario['status'] ?? '') === 'ativo' ? 'Ativo' : 'Inativo') ?></td>
                            <td><?= esc($usuario['criado_em'] ?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();

require __DIR__ . '/../layouts/main.php';
