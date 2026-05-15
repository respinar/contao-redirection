<?php

declare(strict_types=1);

use Contao\DataContainer;
use Contao\DC_Table;

$GLOBALS['TL_DCA']['tl_redirection'] = [
    'config' => [
        'dataContainer' => DC_Table::class,
        'enableVersioning' => true,
        'sql' => [
            'keys' => [
                'id' => 'primary',
                'source_url,active' => 'index',
            ],
        ],
    ],
    'list' => [
        'sorting' => [
            'fields' => ['tstamp'],
            'flag' => DataContainer::SORT_DESC,
            'panelLayout' => 'filter;sort,search,limit',
        ],
        'label' => [
            'fields' => ['source_url', 'target_url', 'status_code'],
            'showColumns' => true,
        ],
        'operations' => [
            'edit' => ['href' => 'act=edit', 'icon' => 'edit.svg'],
            'delete' => ['href' => 'act=delete', 'icon' => 'delete.svg'],
        ],
    ],
    'palettes' => [
        'default' => '{redirect_legend},source_url,target_url,match_type;{settings_legend},status_code,active',
    ],
    'fields' => [
        'id' => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'tstamp' => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'source_url' => [
            'label' => &$GLOBALS['TL_LANG']['tl_redirection']['source_url'],
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'tl_class' => 'w50', 'maxlength' => 255, 'decodeEntities' => true],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'target_url' => [
            'label' => &$GLOBALS['TL_LANG']['tl_redirection']['target_url'],
            'search' => true,
            'inputType' => 'text',
            'eval' => ['decodeEntities' => true, 'maxlength' => 2048, 'dcaPicker' => true, 'tl_class' => 'w50'],
            'sql' => "varchar(2048) NOT NULL default ''",
        ],
        'match_type' => [
            'label' => &$GLOBALS['TL_LANG']['tl_redirection']['match_type'],
            'inputType' => 'select',
            'options' => ['exact', 'wildcard'],
            'reference' => &$GLOBALS['TL_LANG']['tl_redirection']['match_types'],
            'eval' => ['tl_class' => 'w50'],
            'sql' => "varchar(16) NOT NULL default 'exact'",
        ],
        'status_code' => [
            'label' => &$GLOBALS['TL_LANG']['tl_redirection']['status_code'],
            'inputType' => 'select',
            'options' => ['301', '302', '410'],
            'reference' => &$GLOBALS['TL_LANG']['tl_redirection']['status_codes'],
            'eval' => ['tl_class' => 'w50'],
            'sql' => "varchar(3) NOT NULL default '301'",
        ],
        'active' => [
            'label' => &$GLOBALS['TL_LANG']['tl_redirection']['active'],
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50'],
            'sql' => "char(1) NOT NULL default '1'",
        ],
    ],
];
