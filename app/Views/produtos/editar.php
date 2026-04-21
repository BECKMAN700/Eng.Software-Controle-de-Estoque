<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Produto</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 680px;
            margin: 30px auto;
            padding: 0 20px;
            color: #1a1a1a;
        }

        h1 { margin-bottom: 24px; }

        p { margin: 0 0 16px; }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 4px;
            font-size: 0.9rem;
        }

        input[type="text"],
        input[type="number"],
        textarea,
        select {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 0.95rem;
        }

        /* ── Seção de limites de estoque ── */
        .secao-limites {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 6px;
            padding: 16px 20px;
            margin-bottom: 20px;
        }

        .secao-limites h3 {
            margin: 0 0 12px;
            color: #0369a1;
            font-size: 1rem;
        }

        .linha-limites {
            display: flex;
            gap: 16px;
            margin-bottom: 12px;
        }

        .linha-limites > div { flex: 1; }

        /* ── Botão de sugestão ── */
        #btn-sugerir {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #0284c7;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 8px 16px;
            font-size: 0.9rem;
            cursor: pointer;
            transition: background 0.2s;
        }

        #btn-sugerir:hover  { background: #0369a1; }
        #btn-sugerir:disabled { background: #94a3b8; cursor: not-allowed; }

        /* ── Card de sugestão ── */
        #card-sugestao {
            display: none;
            background: #ecfdf5;
            border: 1px solid #6ee7b7;
            border-radius: 6px;
            padding: 14px 16px;
            margin-top: 14px;
            font-size: 0.88rem;
        }

        #card-sugestao .stat-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            margin-bottom: 12px;
        }

        #card-sugestao .stat {
            background: #fff;
            border-radius: 4px;
            padding: 8px 10px;
            text-align: center;
        }

        #card-sugestao .stat .valor {
            font-size: 1.3rem;
            font-weight: bold;
            color: #065f46;
        }

        #card-sugestao .stat .rotulo {
            font-size: 0.75rem;
            color: #374151;
        }

        #card-sugestao .sugestoes {
            display: flex;
            gap: 12px;
        }

        #card-sugestao .sugestao-item {
            flex: 1;
            background: #d1fae5;
            border-radius: 4px;
            padding: 10px 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        #card-sugestao .sugestao-item strong { color: #047857; }

        #btn-aplicar {
            background: #059669;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 6px 14px;
            font-size: 0.85rem;
            cursor: pointer;
            transition: background 0.2s;
        }

        #btn-aplicar:hover { background: #047857; }

        /* ── Alertas ── */
        #aviso-sugestao {
            display: none;
            margin-top: 10px;
            padding: 10px 14px;
            border-radius: 4px;
            font-size: 0.88rem;
        }

        #aviso-sugestao.erro {
            background: #fee2e2;
            border: 1px solid #fca5a5;
            color: #991b1b;
        }

        /* ── Botões do formulário ── */
        .acoes-form {
            display: flex;
            gap: 12px;
            margin-top: 8px;
        }

        button[type="submit"] {
            background: #1d4ed8;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 10px 24px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.2s;
        }

        button[type="submit"]:hover { background: #1e40af; }

        .acoes-form a {
            padding: 10px 18px;
            border: 1px solid #ccc;
            border-radius: 5px;
            text-decoration: none;
            color: #374151;
            font-size: 1rem;
            transition: background 0.2s;
        }

        .acoes-form a:hover { background: #f3f4f6; }
    </style>
</head>
<body>
    <h1>Editar Produto</h1>

    <form action="index.php?acao=atualizar" method="POST">
        <input type="hidden" name="id" value="<?= $produto['id'] ?>">

        <p>
            <label>Nome:</label>
            <input type="text" name="nome" value="<?= htmlspecialchars($produto['nome']) ?>" required>
        </p>

        <p>
            <label>Código:</label>
            <input type="text" name="codigo" value="<?= htmlspecialchars($produto['codigo'] ?? '') ?>">
        </p>

        <p>
            <label>Quantidade:</label>
            <input type="number" name="quantidade" min="0" value="<?= $produto['quantidade'] ?>" required>
        </p>

        <p>
            <label>Preço:</label>
            <input type="number" name="preco" step="0.01" min="0" value="<?= $produto['preco'] ?>" required>
        </p>

        <p>
            <label>Categoria:</label>
            <input type="text" name="categoria" value="<?= htmlspecialchars($produto['categoria'] ?? '') ?>">
        </p>

        <p>
            <label>Unidade:</label>
            <input type="text" name="unidade" value="<?= htmlspecialchars($produto['unidade'] ?? '') ?>">
        </p>

        <p>
            <label>Descrição:</label>
            <textarea name="descricao" rows="3"><?= htmlspecialchars($produto['descricao'] ?? '') ?></textarea>
        </p>

        <p>
            <label>Status:</label>
            <select name="status" required>
                <option value="ativo"          <?= (($produto['status'] ?? '') === 'ativo')          ? 'selected' : '' ?>>Ativo</option>
                <option value="inativo"        <?= (($produto['status'] ?? '') === 'inativo')        ? 'selected' : '' ?>>Inativo</option>
                <option value="descontinuado"  <?= (($produto['status'] ?? '') === 'descontinuado')  ? 'selected' : '' ?>>Descontinuado</option>
            </select>
        </p>

        <!-- ===== SEÇÃO: LIMITES DE ESTOQUE ===== -->
        <div class="secao-limites">
            <h3>📊 Limites de Estoque</h3>

            <div class="linha-limites">
                <div>
                    <label for="estoque_minimo">Estoque Mínimo</label>
                    <input
                        type="number"
                        id="estoque_minimo"
                        name="estoque_minimo"
                        min="0"
                        value="<?= $produto['estoque_minimo'] !== null ? (int)$produto['estoque_minimo'] : '' ?>"
                        placeholder="Ex: 50"
                    >
                </div>
                <div>
                    <label for="estoque_maximo">Estoque Máximo</label>
                    <input
                        type="number"
                        id="estoque_maximo"
                        name="estoque_maximo"
                        min="0"
                        value="<?= $produto['estoque_maximo'] !== null ? (int)$produto['estoque_maximo'] : '' ?>"
                        placeholder="Ex: 200"
                    >
                </div>
            </div>

            <!-- Botão de sugestão -->
            <button type="button" id="btn-sugerir">
                ✨ Sugerir limites pelo histórico de entradas
            </button>

            <!-- Aviso de erro -->
            <div id="aviso-sugestao" class="erro"></div>

            <!-- Card com o resultado da sugestão -->
            <div id="card-sugestao">
                <!-- Badge do método utilizado -->
                <div id="badge-metodo" style="
                    display:inline-block;
                    margin-bottom:10px;
                    padding:4px 10px;
                    border-radius:20px;
                    font-size:0.78rem;
                    font-weight:bold;
                    background:#dbeafe;
                    color:#1d4ed8;
                "></div>

                <div class="stat-grid">
                    <div class="stat">
                        <div class="valor" id="stat-total">—</div>
                        <div class="rotulo">Entradas registradas</div>
                    </div>
                    <div class="stat">
                        <div class="valor" id="stat-consumo">—</div>
                        <div class="rotulo">Consumo médio/dia</div>
                    </div>
                    <div class="stat">
                        <div class="valor" id="stat-prazo">—</div>
                        <div class="rotulo">Prazo médio entre entregas</div>
                    </div>
                    <div class="stat">
                        <div class="valor" id="stat-media">—</div>
                        <div class="rotulo">Média por entrada</div>
                    </div>
                    <div class="stat">
                        <div class="valor" id="stat-saidas">—</div>
                        <div class="rotulo">Total de saídas</div>
                    </div>
                    <div class="stat">
                        <div class="valor" id="stat-range">—</div>
                        <div class="rotulo">Variação entrada (min–máx)</div>
                    </div>
                </div>

                <div class="sugestoes">
                    <div class="sugestao-item">
                        <div>
                            <div class="rotulo">Mínimo sugerido</div>
                            <strong id="val-minimo">—</strong>
                        </div>
                        <button type="button" id="btn-aplicar-min" onclick="aplicar('min')">Aplicar</button>
                    </div>
                    <div class="sugestao-item">
                        <div>
                            <div class="rotulo">Máximo sugerido</div>
                            <strong id="val-maximo">—</strong>
                        </div>
                        <button type="button" id="btn-aplicar-max" onclick="aplicar('max')">Aplicar</button>
                    </div>
                    <div class="sugestao-item" style="justify-content:center;">
                        <button type="button" id="btn-aplicar" onclick="aplicarAmbos()">Aplicar ambos</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- ===== FIM DA SEÇÃO DE LIMITES ===== -->

        <div class="acoes-form">
            <button type="submit">Atualizar</button>
            <a href="index.php?acao=listar">Voltar</a>
        </div>
    </form>

    <script>
        const produtoId  = <?= (int) $produto['id'] ?>;
        const btnSugerir = document.getElementById('btn-sugerir');
        const cardSugest = document.getElementById('card-sugestao');
        const avisoEl    = document.getElementById('aviso-sugestao');

        const metodoLabels = {
            'consumo_x_prazo' : '📐 Fórmula: Consumo diário × Prazo médio de reposição',
            'consumo_x_7dias' : '📐 Fórmula: Consumo diário × 7 dias (prazo padrão)',
            'fallback_30pct'  : '⚠️ Sem saídas registradas — usando 30% da média de entradas',
        };

        let dadosSugestao = null;

        btnSugerir.addEventListener('click', async () => {
            btnSugerir.disabled = true;
            btnSugerir.textContent = '⏳ Calculando…';
            cardSugest.style.display = 'none';
            avisoEl.style.display    = 'none';

            try {
                const resp = await fetch(`index.php?acao=sugerir_limites&id=${produtoId}`);
                const data = await resp.json();

                if (data.erro) {
                    avisoEl.textContent   = '⚠️ ' + data.erro;
                    avisoEl.style.display = 'block';
                } else {
                    dadosSugestao = data;

                    // Badge do método
                    const badge = document.getElementById('badge-metodo');
                    badge.textContent = metodoLabels[data.metodo_minimo] ?? data.metodo_minimo;

                    // Stats
                    document.getElementById('stat-total').textContent   = data.total_entradas;
                    document.getElementById('stat-media').textContent   = data.media_entrada;
                    document.getElementById('stat-range').textContent   = data.menor_entrada + ' – ' + data.maior_entrada;
                    document.getElementById('stat-saidas').textContent  = data.total_saidas ?? '—';
                    document.getElementById('stat-consumo').textContent = data.consumo_diario !== null
                        ? data.consumo_diario + ' un/dia'
                        : '—';
                    document.getElementById('stat-prazo').textContent   = data.prazo_reposicao_dias !== null
                        ? data.prazo_reposicao_dias + ' dias'
                        : '—';

                    // Sugestões
                    document.getElementById('val-minimo').textContent = data.minimo_sugerido;
                    document.getElementById('val-maximo').textContent = data.maximo_sugerido;

                    cardSugest.style.display = 'block';
                }
            } catch (err) {
                avisoEl.textContent   = '⚠️ Erro ao buscar sugestão. Tente novamente.';
                avisoEl.style.display = 'block';
            } finally {
                btnSugerir.disabled    = false;
                btnSugerir.textContent = '✨ Sugerir limites pelo histórico de entradas';
            }
        });

        function aplicar(tipo) {
            if (!dadosSugestao) return;
            if (tipo === 'min') {
                document.getElementById('estoque_minimo').value = dadosSugestao.minimo_sugerido;
            } else {
                document.getElementById('estoque_maximo').value = dadosSugestao.maximo_sugerido;
            }
        }

        function aplicarAmbos() {
            if (!dadosSugestao) return;
            document.getElementById('estoque_minimo').value = dadosSugestao.minimo_sugerido;
            document.getElementById('estoque_maximo').value = dadosSugestao.maximo_sugerido;
        }
    </script>
</body>
</html>