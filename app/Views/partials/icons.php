<?php
if (!function_exists('uiIcon')) {
    function uiIcon(string $name, string $class = 'icon'): string
    {
        $icons = [
            'dashboard' => '<rect width="7" height="9" x="3" y="3" rx="1"></rect><rect width="7" height="5" x="14" y="3" rx="1"></rect><rect width="7" height="9" x="14" y="12" rx="1"></rect><rect width="7" height="5" x="3" y="16" rx="1"></rect>',
            'box' => '<path d="m21 8-9-5-9 5 9 5 9-5Z"></path><path d="m3 8 9 5 9-5"></path><path d="m3 8v8l9 5 9-5V8"></path><path d="M12 13v8"></path>',
            'catalog' => '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M4 4.5A2.5 2.5 0 0 1 6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15Z"></path>',
            'reports' => '<path d="M3 3v18h18"></path><path d="M7 15v-5"></path><path d="M12 15V7"></path><path d="M17 15v-3"></path>',
            'package-plus' => '<path d="m21 8-9-5-9 5 9 5 9-5Z"></path><path d="m3 8 9 5 9-5"></path><path d="m3 8v8l9 5 4-2.2"></path><path d="M12 13v8"></path><path d="M17 18h5"></path><path d="M19.5 15.5v5"></path>',
            'list' => '<path d="M8 6h13"></path><path d="M8 12h13"></path><path d="M8 18h13"></path><path d="M3 6h.01"></path><path d="M3 12h.01"></path><path d="M3 18h.01"></path>',
            'alert' => '<path d="m21.73 18-8-14a2 2 0 0 0-3.46 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path><path d="M12 9v4"></path><path d="M12 17h.01"></path>',
            'movement' => '<path d="M8 7h12"></path><path d="m16 3 4 4-4 4"></path><path d="M16 17H4"></path><path d="m8 13-4 4 4 4"></path>',
            'inventory' => '<rect width="8" height="4" x="8" y="2" rx="1"></rect><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><path d="M8 11h8"></path><path d="M8 16h5"></path>',
            'users' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>',
            'logout' => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><path d="m16 17 5-5-5-5"></path><path d="M21 12H9"></path>',
            'search' => '<circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.3-4.3"></path>',
            'plus' => '<path d="M5 12h14"></path><path d="M12 5v14"></path>',
            'chevron-left' => '<path d="m15 18-6-6 6-6"></path>',
            'chevron-right' => '<path d="m9 18 6-6-6-6"></path>',
            'corner-down-left' => '<polyline points="9 10 4 15 9 20"></polyline><path d="M20 4v7a4 4 0 0 1-4 4H4"></path>',
            'more' => '<circle cx="12" cy="5" r="1.6" fill="currentColor" stroke="none"></circle><circle cx="12" cy="12" r="1.6" fill="currentColor" stroke="none"></circle><circle cx="12" cy="19" r="1.6" fill="currentColor" stroke="none"></circle>',
            'eye' => '<path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path><circle cx="12" cy="12" r="3"></circle>',
            'eye-off' => '<path d="M9.88 9.88a3 3 0 0 0 4.24 4.24"></path><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"></path><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"></path><line x1="2" x2="22" y1="2" y2="22"></line>',
            'moon' => '<path d="M12 3a6 6 0 0 0 9 7.35A9 9 0 1 1 12 3Z"></path>',
            'sun' => '<circle cx="12" cy="12" r="4"></circle><path d="M12 2v2"></path><path d="M12 20v2"></path><path d="m4.93 4.93 1.41 1.41"></path><path d="m17.66 17.66 1.41 1.41"></path><path d="M2 12h2"></path><path d="M20 12h2"></path><path d="m6.34 17.66-1.41 1.41"></path><path d="m19.07 4.93-1.41 1.41"></path>',
        ];

        $body = $icons[$name] ?? '<circle cx="12" cy="12" r="8"></circle>';
        $safeClass = htmlspecialchars($class, ENT_QUOTES, 'UTF-8');

        return '<span class="' . $safeClass . '" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">' . $body . '</svg></span>';
    }
}

if (!function_exists('uiEmptyState')) {
    /**
     * Estado vazio padronizado: ícone + título + descrição + ação opcional.
     */
    function uiEmptyState(string $icone, string $titulo, string $descricao = '', string $acaoLabel = '', string $acaoUrl = ''): string
    {
        $e = static function (string $v): string {
            return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
        };

        $html = '<div class="empty-state">';
        $html .= '<span class="empty-state-icon">' . uiIcon($icone, 'icon') . '</span>';
        $html .= '<p class="empty-state-title">' . $e($titulo) . '</p>';

        if ($descricao !== '') {
            $html .= '<p class="empty-state-desc">' . $e($descricao) . '</p>';
        }

        if ($acaoLabel !== '' && $acaoUrl !== '') {
            $html .= '<a class="btn btn-primary" href="' . $e($acaoUrl) . '">' . $e($acaoLabel) . '</a>';
        }

        $html .= '</div>';

        return $html;
    }
}
