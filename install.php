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

db_extend('packages');

if (empty($context['uninstalling'])){

	$table = array(
		'table_name' => 'autorespond',
		'columns' => array(
			array(
				'name' => 'id',
				'type' => 'int',
				'size' => 5,
				'null' => false,
				'auto' => true
			),
			array(
				'name' => 'board_id',
				'type' => 'varchar',
				'size' => 255,
				'default' => '',
			),
			array(
				'name' => 'user_id',
				'type' => 'int',
				'size' => 5,
				'null' => false,
			),
			array(
				'name' => 'title',
				'type' => 'varchar',
				'size' => 255,
				'default' => '',
			),
			array(
				'name' => 'body',
				'type' => 'text',
				'size' => '',
				'default' => '',
			),
		),
		'indexes' => array(
			array(
				'type' => 'primary',
				'columns' => array('id')
			),
		),
		'if_exists' => 'ignore',
		'error' => 'fatal',
		'parameters' => array(),
	);

	$smcFunc['db_create_table']($db_prefix . $table['table_name'], $table['columns'], $table['indexes'], $table['parameters'], $table['if_exists'], $table['error']);
}
