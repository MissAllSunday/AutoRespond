<?php

/**
 * Auto respond mod (SMF)
 *
 * @package AutoRespond
*  @version 2.1.1
 * @author Michel Mendiola <suki@missallsunday.com>
 * @copyright Copyright (c) 2024  Michel Mendiola
 * @license https://www.mozilla.org/en-US/MPL/2.0/
 */


if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
    require_once(dirname(__FILE__) . '/SSI.php');

elseif (!defined('SMF'))
    exit('<b>Error:</b> Cannot install - please verify you put this in the same place as SMF\'s index.php.');

global $smcFunc, $context, $db_prefix;


db_extend('packages');

if (!empty($context['uninstalling'])) {
    return;
}

$table = [
    'table_name' => '{db_prefix}autorespond',
    'columns' => [
        [
            'name' => 'id',
            'type' => 'int',
            'size' => 5,
            'null' => false,
            'auto' => true
        ],
        [
            'name' => 'board_id',
            'type' => 'varchar',
            'size' => 255,
            'default' => '',
        ],
        [
            'name' => 'user_id',
            'type' => 'int',
            'size' => 5,
            'null' => false,
        ],
        [
            'name' => 'title',
            'type' => 'varchar',
            'size' => 255,
            'default' => '',
        ],
        [
            'name' => 'body',
            'type' => 'text',
            'size' => '',
            'default' => null,
        ],
    ],
    'indexes' => [
        [
            'type' => 'primary',
            'columns' => ['id']
        ],
    ],
    'if_exists' => 'ignore',
    'error' => 'fatal',
    'parameters' => [],
];

$smcFunc['db_create_table'](
    $table['table_name'],
    $table['columns'],
    $table['indexes'],
    $table['parameters'],
    $table['if_exists'],
    $table['error']
);