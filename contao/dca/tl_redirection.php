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
            'fields' => ['source_url', 'wildcard', 'target_url', 'status_code'],
            'showColumns' => true,
        ],
        'operations' => [
            'edit' => [
                'href' => 'act=edit',
                'icon' => 'edit.svg',
            ],
            'delete' => [
                'href' => 'act=delete',
                'icon' => 'delete.svg',
            ],
        ],
    ],
    'palettes' => [
        'default' => '{status_legend},status_code,wildcard;{redirect_legend},source_url;{settings_legend},active',
    ],
    'subpalettes' => [
        'status_code_301' => 'target_url',
        'status_code_302' => 'target_url',
    ],
    'fields' => [
        'id' => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'tstamp' => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'status_code' => [
            'inputType' => 'select',
            'options' => ['301', '302', '410'],
            'reference' => &$GLOBALS['TL_LANG']['tl_redirection']['status_codes'],
            'eval' => [
                'submitOnChange' => true,
                'tl_class' => 'w50',
            ],
            'sql' => "varchar(3) NOT NULL default '302'",
        ],
        'source_url' => [
            'inputType' => 'text',
            'eval' => [
                'mandatory' => true,
                'tl_class' => 'w50',
                'maxlength' => 255,
                'decodeEntities' => true,
            ],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'wildcard' => [
            'inputType' => 'checkbox',
            'eval' => [
                'tl_class' => 'w50 m12',
            ],
            'sql' => "char(1) NOT NULL default ''",
        ],
        'target_url' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => [
                'mandatory' => true,
                'decodeEntities' => true,
                'maxlength' => 2048,
                'dcaPicker' => true,
                'tl_class' => 'w50',
            ],
            'sql' => "varchar(2048) NOT NULL default ''",
        ],
        'active' => [
            'inputType' => 'checkbox',
            'eval' => [
                'tl_class' => 'w50 m12',
            ],
            'sql' => "char(1) NOT NULL default '1'",
        ],
    ],
];
