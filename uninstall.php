<?php

/**
 * Auto respond mod (SMF)
 *
 * @package AutoRespond
 * @version 2.1
 * @author Jessica González <suki@missallsunday.com>
 * @copyright Copyright (c) 2024  Michel Mendiola
 * @license https://opensource.org/license/mit/
 */

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');
elseif (!defined('SMF'))
	exit('<b>Error:</b> Cannot install - please verify you put this in the same place as SMF\'s index.php.');

$hooks = array(
	'integrate_pre_include' => '$sourcedir/AutoRespond.php',
	'integrate_admin_areas' => 'AutoRespondAdmin',
);

	$call = 'remove_integration_function';


foreach ($hooks as $hook => $function)
	$call($hook, $function);
