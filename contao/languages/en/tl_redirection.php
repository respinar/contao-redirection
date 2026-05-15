<?php

declare(strict_types=1);

$GLOBALS['TL_LANG']['tl_redirection'] = [
    'redirect_legend' => 'Redirect',
    'settings_legend' => 'Settings',

    'source_url' => ['Source URL', 'The URL path to redirect from, without a leading slash (e.g. old page or old-page).'],
    'target_url' => ['Target URL', 'The destination URL. You may use insert tags (e.g. {{link_url::4}}) or wildcard placeholders ($1, $2, ...) for wildcard matches. Ignored for 410.'],
    'match_type' => ['Match type', 'How the source URL should be matched.'],
    'status_code' => ['HTTP status', '301 = permanent, 302 = temporary, 410 = gone.'],
    'active' => ['Active', 'Enable this redirect.'],
    'status_codes' => [
        '301' => '301 - Permanent redirect',
        '302' => '302 - Temporary redirect',
        '410' => '410 - Gone',
    ],
    'match_types' => [
        'exact' => 'Exact match',
        'wildcard' => 'Wildcard (regular expression)',
    ],
];
