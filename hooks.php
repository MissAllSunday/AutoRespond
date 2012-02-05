<?php

/**
 * Auto respond mod (SMF)
 *
 * @package SMF
 * @author Miss All Sunday
 *
 * @copyright 2011 Miss All Sunday
 * @license http://creativecommons.org/licenses/by-nc-sa/3.0/ CC BY-NC-SA 3.0
 *
 * @version 2.0
 */

	if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
		require_once(dirname(__FILE__) . '/SSI.php');
	elseif (!defined('SMF'))
		exit('<b>Error:</b> Cannot install - please verify you put this in the same place as SMF\'s index.php.');

	$hooks = array(
		'integrate_pre_include' => '$sourcedir/AutoRespond.php',
		'integrate_admin_areas' => 'AutoRespondAdmin',
	);

		$call = 'add_integration_function';

	foreach ($hooks as $hook => $function)
		$call($hook, $function);

?>