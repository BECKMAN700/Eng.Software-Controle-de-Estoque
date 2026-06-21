# Design System — Controle de Estoque

Documentação dos tokens e padrões visuais do sistema. Todos os tokens são
definidos como **CSS Custom Properties** em [`public/assets/css/base.css`](../public/assets/css/base.css)
no seletor `:root` (tema claro) e sobrescritos em `html[data-theme="dark"]`
(tema escuro). Use sempre os tokens — nunca valores "soltos" (hex/px) direto nas regras.

---

## 1. Identidade da marca

- **Símbolo + wordmark:** [`public/assets/img/logo.svg`](../public/assets/img/logo.svg)
  — cubo de camadas empilhadas (estoque) usado na sidebar, login e favicon.
- **Cor de marca:** azul `#2563eb` (`--primary`) — transmite confiança/gestão.
- **Cor de destaque:** teal `#14b8a6` (`--accent`) — usada na face do cubo,
  em gráficos e estados ativos, evitando que tudo seja azul.
- Cabeçalho de marca nos PDFs exportados: helper `brandHeader()` em
  [`app/Controllers/RelatorioController.php`](../app/Controllers/RelatorioController.php).

---

## 2. Cores

| Token | Claro | Escuro | Uso |
|-------|-------|--------|-----|
| `--primary` | `#2563eb` | `#60a5fa` | Cor de marca, botões primários, links |
| `--primary-dark` | `#1d4ed8` | `#3b82f6` | Hover de primário |
| `--primary-soft` | `#eff6ff` | `rgba(96,165,250,.16)` | Fundos sutis, badges info |
| `--accent` | `#14b8a6` | `#2dd4bf` | Destaque secundário, gráficos, ativos |
| `--accent-dark` | `#0d9488` | `#14b8a6` | Hover do destaque |
| `--accent-soft` | `#f0fdfa` | `rgba(45,212,191,.16)` | Fundos de destaque |
| `--success` | `#16a34a` | `#4ade80` | Sucesso, entradas |
| `--warning` | `#d97706` | `#fbbf24` | Atenção, alertas de estoque |
| `--danger` | `#dc2626` | `#f87171` | Erro, saídas, exclusões |

Superfícies e texto: `--bg-page`, `--bg-card`, `--bg-soft`, `--bg-muted`,
`--bg-elevated`, `--bg-sidebar`, `--text-main`, `--text-muted`, `--text-light`,
`--border`. Cada `*-soft` tem um par para fundos discretos.

---

## 3. Tipografia

Fonte: **Inter** (fallback system-ui) via `--font-sans`.

### Escala (tokens + utilitários)

| Nível | Token | Tamanho | Peso | Classe utilitária |
|-------|-------|---------|------|-------------------|
| Display | `--text-display` | 30px | 800 (`--weight-black`) | `.text-display` |
| H1 | `--text-h1` | 25px | 700 (`--weight-bold`) | `.text-h1` |
| H2 | `--text-h2` | 19px | 700 | `.text-h2` |
| Body | `--text-body` | 15px | 400 (`--weight-regular`) | `.text-body` |
| Caption | `--text-caption` | 13px | 500 (`--weight-medium`) | `.text-caption` |

Os elementos `h1`, `h2`, `h3` já recebem a escala por padrão em `base.css`
(classes específicas podem sobrepor). Aplicações: Display → valores de métrica
e título do login; H1 → título da topbar e de página; H2 → títulos de card.

### Pesos
`--weight-regular: 400` · `--weight-medium: 500` · `--weight-semibold: 600`
· `--weight-bold: 700` · `--weight-black: 800`.

---

## 4. Espaçamento

Escala base de 4px: `--space-1: 4px` · `--space-2: 8px` · `--space-3: 12px`
· `--space-4: 16px` · `--space-5: 24px` · `--space-6: 32px`.

---

## 5. Raio de borda

`--radius-sm: 8px` (campos pequenos) · `--radius-md: 14px` (tabelas, alertas)
· `--radius-lg: 20px` (cards).

---

## 6. Elevação

Cards usam sempre a **mesma receita**: `border: 1px solid var(--border)` +
`box-shadow: var(--shadow-sm)` + `border-radius: var(--radius-lg)`.

- `--shadow-sm` — repouso (cards, métricas, painéis).
- `--shadow-md` — hover/elevado (cards interativos).

**No tema escuro a elevação é por cor, não por sombra forte:** o card
(`--bg-card`) é um tom mais claro que o fundo da página (`--bg-page`), e o hover
sobe para `--bg-elevated`. As sombras no dark ficam discretas de propósito.

---

## 7. Componentes principais

Definidos em [`components.css`](../public/assets/css/components.css):
`.btn` (+`-primary/-secondary/-success/-warning/-danger/-sm`), `.card`,
`.metric-card`, `.badge` (+ variantes), `.alert`, `.table`, `.form-group`,
`.empty-state`. Layout em [`layout.css`](../public/assets/css/layout.css)
(sidebar, topbar, app-shell) e estilos de página em
[`pages.css`](../public/assets/css/pages.css).

---

## 8. Convenções

- **Sem estilos inline** no HTML/PHP — tudo via classes.
- Cores, tamanhos, espaçamentos, raios e sombras **sempre** via token `var(--…)`.
- Temas claro/escuro alternados por `html[data-theme]`; nunca fixe cores que
  precisem mudar entre temas.
