<?php

declare(strict_types=1);

/*
 * This file is part of Contao Redirects Bundle.
 *
 * (c) Hamid Peywasti
 *
 * @license MIT
 */

use Contao\DataContainer;
use Contao\DC_Table;
use Respinar\RedirectsBundle\Repository\RedirectsRepository;

$GLOBALS['TL_DCA']['tl_redirects'] = [
    'config' => [
        'dataContainer' => DC_Table::class,
        'enableVersioning' => true,
        'oncreate_callback' => [
            [RedirectsRepository::class, 'clearCache'],
        ],
        'ondelete_callback' => [
            [RedirectsRepository::class, 'clearCache'],
        ],
        'oncut_callback' => [
            [RedirectsRepository::class, 'clearCache'],
        ],
        'onsubmit_callback' => [
            [RedirectsRepository::class, 'clearCache'],
        ],
        'sql' => [
            'keys' => [
                'id' => 'primary',
                'source_url,published' => 'index',
            ],
        ],
    ],
    'list' => [
        'sorting' => [
            'mode' => DataContainer::MODE_SORTABLE,
            'fields' => ['dateAdded'],
            'flag' => DataContainer::SORT_DAY_DESC,
            'panelLayout' => 'filter;sort,search,limit',
        ],
        'label' => [
            'fields' => ['source_url', 'wildcard', 'target_url', 'status_code', 'dateAdded'],
            'showColumns' => true,
        ],
    ],
    'palettes' => [
        '__selector__' => ['status_code'],
        '301' => '{status_legend},status_code;{redirect_legend},source_url,wildcard,target_url;{publish_legend},published',
        '302' => '{status_legend},status_code;{redirect_legend},source_url,wildcard,target_url;{publish_legend},published',
        '410' => '{status_legend},status_code;{redirect_legend},source_url,wildcard;{publish_legend},published',
    ],
    'fields' => [
        'id' => [
            'sql' => ['type' => 'integer', 'unsigned' => true, 'notnull' => true, 'autoincrement' => true],
        ],
        'tstamp' => [
            'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
        ],
        'status_code' => [
            'filter' => true,
            'inputType' => 'select',
            'options' => ['301', '302', '410'],
            'reference' => &$GLOBALS['TL_LANG']['tl_redirects']['status_codes'],
            'eval' => ['submitOnChange' => true, 'tl_class' => 'w50'],
            'sql' => ['type' => 'string', 'length' => 3, 'notnull' => true, 'default' => '301'],
        ],
        'source_url' => [
            'search' => true,
            'sorting' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'tl_class' => 'w50', 'maxlength' => 255, 'decodeEntities' => true, 'unique'=>true],
            'sql' => ['type' => 'string', 'length' => 255, 'notnull' => true, 'default' => ''],
        ],
        'wildcard' => [
            'filter' => true,
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50 m12'],
            'sql' => ['type' => 'boolean', 'default' => false],
        ],
        'target_url' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'decodeEntities' => true, 'maxlength' => 2048, 'dcaPicker' => true, 'tl_class' => 'w50'],
            'sql' => ['type' => 'string', 'length' => 2048, 'notnull' => true, 'default' => ''],
        ],
        'dateAdded' => [
            'default' => time(),
            'sorting' => true,
            'flag' => DataContainer::SORT_DAY_DESC,
            'eval' => ['rgxp' => 'datim', 'doNotCopy' => true],
            'sql' => ['type' => 'integer', 'unsigned' => true, 'notnull' => true, 'default' => 0],
        ],
        'published' => [
            'toggle' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'eval' => ['doNotCopy' => true],
            'sql' => ['type' => 'boolean', 'default' => false],
        ],
    ],
];
