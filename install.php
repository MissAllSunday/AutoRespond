<?php

/**
 * Auto respond mod (SMF)
 *
 * @package AutoRespond
 * @version 2.0.2
 * @author Jessica González <suki@missallsunday.com>
 * @copyright Copyright (c) 2017 Jessica González
 * @license http://www.mozilla.org/MPL/ MPL 2.0
 */


if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');

elseif (!defined('SMF'))
	exit('<b>Error:</b> Cannot install - please verify you put this in the same place as SMF\'s index.php.');

global $smcFunc, $context, $db_prefix;

function installCheck(): void
{
	if (version_compare(\PHP_VERSION, '7.4.0', '<')) {
		fatal_error('This mod needs PHP 7.4 or greater.
		 You will not be able to install/use this mod,contact your host and ask for a PHP upgrade.');
	}
}

installCheck();

db_extend('packages');

if (!empty($context['uninstalling'])) {
	return;
}

$table = [
	'table_name' => 'autorespond',
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
			'type' => 'int',
			'size' => 5,
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
			'default' => '',
		],
	],
	'indexes' => [
		[
			'type' => 'primary',
			'columns' => ['id', 'board_id']
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