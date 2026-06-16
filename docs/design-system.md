# Design System - Controle de Estoque

Guia visual do sistema. Centraliza os tokens (variaveis CSS), a tipografia, os
componentes e as regras de acessibilidade, para manter o frontend consistente e
profissional. Os tokens ficam em `public/assets/css/base.css`.

## 1. Principios

- **Clareza antes de decoracao** - a interface serve a leitura dos dados.
- **Dados em primeiro plano** - numeros alinhados, hierarquia clara, pouco ruido.
- **Consistencia via tokens** - cores, espacos e raios vem de variaveis, nunca de valores soltos.
- **Acessivel por padrao** - contraste, foco visivel e navegacao por teclado.

## 2. Cores (tokens)

Cada cor tem uma variante "soft" para fundos suaves. No tema escuro, os mesmos
tokens sao redefinidos em `html[data-theme="dark"]`.

| Token | Claro | Uso |
| --- | --- | --- |
| `--primary` | `#2563eb` | Acao principal, links, foco |
| `--success` | `#16a34a` | Sucesso, alto giro, estoque ok |
| `--warning` | `#d97706` | Atencao, estoque no minimo |
| `--danger` | `#dc2626` | Erro, estoque critico, excluir |
| `--text-main` | `#101828` | Texto principal |
| `--text-muted` | `#667085` | Texto secundario |
| `--bg-page` | `#f5f7fb` | Fundo da pagina |
| `--bg-card` | `#ffffff` | Cartoes e superficies |
| `--bg-muted` | `#f2f4f7` | Pills e areas neutras |
| `--border` | `#e5e7eb` | Bordas e divisores |

> Regra: **nunca** usar hex fixo em texto/fundo de conteudo. Use sempre o token,
> para o tema escuro funcionar automaticamente.

## 3. Tipografia

- Fonte: **Inter** com fallback de sistema (`--font-sans`).
- Escala sugerida:

| Nivel | Tamanho | Peso |
| --- | --- | --- |
| Display (metricas) | 30px | 800 |
| Titulo de pagina (h1) | 25px | 800 |
| Titulo de secao (h2/h3) | 19px | 800 |
| Corpo | 15px | 400/600 |
| Legenda | 13px | 400 |

Numeros em tabelas usam `font-variant-numeric: tabular-nums` (classe `.numeric`).

## 4. Espacamento, raio e sombra

- Escala de espaco: `--space-1` (4px) a `--space-6` (32px).
- Raios: `--radius-sm` (8px), `--radius-md` (14px), `--radius-lg` (20px).
- Sombras: `--shadow-sm` (cartoes), `--shadow-md` (elevado/menu).

## 5. Componentes

| Componente | Classe | Observacao |
| --- | --- | --- |
| Botao | `.btn` + `.btn-primary` / `.btn-secondary` / `.btn-danger` / `.btn-sm` | Acao principal sempre `primary` |
| Cartao | `.card`, `.metric-card` | Fundo `--bg-card`, borda e sombra padrao |
| Badge | `.badge` + `-success/-warning/-danger/-muted/-info` | Status compacto |
| Tabela | `.table` (+ `.numeric` nas colunas de numero) | Cabecalho fixo, zebra, hover |
| Formulario | `.form-group`, `.field-invalid` | Validacao inline |
| Alerta | `.alert` + `-success/-danger` | Mensagens de topo |
| Estado vazio | `.empty-state` | Ideal com icone + acao |
| Pill de estoque | `.stock-pill` + `.situacao-*` | Quantidade com cor de situacao |

## 6. Icones

SVG inline pelo helper `uiIcon($nome, $classe)` em `app/Views/partials/icons.php`
(estilo traco, 24x24). Classes `.icon` (18px) e `.nav-icon` (20px).

## 7. Tema escuro

- Alternado pelo botao sol/lua na topbar (`[data-theme-toggle]`).
- Persistido em `localStorage` (`ce-theme`) e respeita `prefers-color-scheme`.
- Script anti-flash no `<head>` aplica o tema antes do CSS carregar.

## 8. Acessibilidade

- `:focus-visible` com anel de foco em todos os elementos interativos.
- Link "pular para o conteudo" (`.skip-link`).
- `prefers-reduced-motion` desliga animacoes.
- Alvos de toque com no minimo 44px de altura.

## 9. Convencoes

- Componentes novos devem reutilizar tokens e classes existentes.
- Evitar `style="..."` inline nas views; criar/estender classes no CSS.
- Mobile: grids viram coluna unica; tabelas rolam horizontalmente no `.table-wrapper`.
