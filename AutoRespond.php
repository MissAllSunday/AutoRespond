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

if (!defined('SMF'))
	die('Hacking attempt...');



function ModifyAutoRespondSettings($return_config = false)
{
	global $txt, $scripturl, $context, $sourcedir;

	/* I can has Adminz? */
	isAllowedTo('admin_forum');

	loadLanguage('AutoRespond');
	loadtemplate('AutoRespond');

	require_once($sourcedir . '/ManageSettings.php');

	$context['page_title'] = $txt['AR_admin_panel'];

	$subActions = array(
		'basic' => 'BasicAutoRespondSettings',
		'list' => 'AutoRespondListPage',
		'add' => 'AutoRespondAdd',
		'add2' => 'AutoRespondAdd2',
		'edit' => 'AutoRespondEdit',
		'edit2' => 'AutoRespondEdit2',
		'delete' => 'AutoRespondDelete'
	);

	loadGeneralSettingParameters($subActions, 'basic');

	// Load up all the tabs...
	$context[$context['admin_menu_name']]['tab_data'] = array(
		'title' => $txt['AR_admin_panel'],
		'description' => $txt['AR_admin_panel_desc'],
		'tabs' => array(
			'basic' => array()
		),
	);

	call_user_func($subActions[$_REQUEST['sa']]);
}



/* This will show a list of all your custom messages */


/* Get the info and show a nice form */
function AutoRespondEdit()
{
	global $txt, $context, $scripturl, $modSettings, $sourcedir;

	/* I can has Adminz? */
	isAllowedTo('admin_forum');
	loadLanguage('AutoRespond');
	loadtemplate('AutoRespond');
	AutoRespondHeaders();

	$validation = AutoRespondValidate();

	require_once($sourcedir . '/OharaDB.class.php');

	/* OK, the data do exist, get it, get it now! */
	if (isset($_REQUEST['arid']) && in_array($_REQUEST['arid'], array_keys($validation)))
	{
		/* Set the parameters for the query */
		$params = array(
			'rows' =>'id, board_id, user_id, title, body',
			'where' => 'id = {int:arid}'
		);
		$data = array(
			'arid' => isset($_REQUEST['arid']) ? $_REQUEST['arid'] : null,
		);

		/* We need this... */
		require_once($sourcedir . '/Subs-Post.php');

		/* Prepare the query */
		$query = new OharaDBClass('autorespond');
		$query->Params($params, $data);

		/* Get the data */
		$query->GetData('id');
		$context['autorespond']['edit'] = $query->data_result[$_REQUEST['arid']];
		$context['autorespond']['edit']['body'] = un_preparsecode($context['autorespond']['edit']['body']);
		$context['autorespond']['edit']['board_id'] = explode(',',$context['autorespond']['edit']['board_id']);

		/* The nice form */
		$context['sub_template'] = 'auto_respond_add';

		/* Page stuff */
		$context['page_title'] = sprintf($txt['AR_admin_editing'], $context['autorespond']['edit']['title']);
		$context['linktree'][] = array(
			'url' => $scripturl. '?action=admin;area=autorespond;sa=edit;arid='.$_REQUEST['arid'] ,
			'name' => sprintf($txt['AR_admin_editing'], $context['autorespond']['edit']['title'])
		);

		/* Load all boards */
		$context['autorespond']['boards'] = AutoRespondBoards();
	}

	/* No data... */
	else
		fatal_lang_error('AR_no_message', false);
}

/* Got the data?  let's do the update then */
function AutoRespondEdit2()
{
	global $txt, $sourcedir;

	/* I can has Adminz? */
	isAllowedTo('admin_forum');
	loadLanguage('AutoRespond');
	loadtemplate('AutoRespond');

	$validation = AutoRespondValidate();

	if (isset($_REQUEST['arid']) && in_array($_REQUEST['arid'], array_keys($validation)))
	{
		/* Safety first! */
		checkSession('post', '', true);

		require_once($sourcedir . '/OharaDB.class.php');

		/* Generic array */
		$update = array();

	/* You need to fill out at least the title, boards and body, user is set to 0 by default */
	if(empty($_POST['body']) || empty($_POST['title']) || empty($_POST['board_id']))
		fatal_lang_error('AR_empty_value', false);

		/* Cleaning! */
		$update['body'] = AutoRespondClean($_POST['body'], true);
		$update['title'] = AutoRespondClean($_POST['title']);
		$update['user'] = (int) $_POST['user_id'];
		$update['board'] = implode(',', $_POST['board_id']);

		/* Update! */
		$params = array(
			'set' =>'board_id={string:board_id}, user_id={int:user_id}, title={string:title}, body={string:body}',
			'where' => 'id = {int:id}',
		);

		$data = array(
			'board_id' => $update['board'],
			'user_id' => $update['user'],
			'title' => $update['title'],
			'body' => $update['body'],
			'id' => $_REQUEST['arid']
		);

		$updatedata = new OharaDBClass('autorespond');
		$updatedata->Params($params, $data);
		$updatedata->UpdateData();

		redirectexit('action=admin;area=autorespond;sa=list');
	}

	/* No data... */
	else
		fatal_lang_error('AR_no_message', false);
}

/* ...As you wish */


/* New stuff */


/* Please Insert 2 Coins To Play */
function AutoRespondAdd2()
{
	global $txt, $context,$sourcedir, $scripturl;

	/* I can has Adminz? */
	isAllowedTo('admin_forum');

	require_once($sourcedir . '/OharaDB.class.php');

	/* Safety first! */
	checkSession('post', '', true);

	$add = array();

	/* You need to fill out at least the title, boards and body, user is set to 0 by default */
	if(empty($_POST['body']) || empty($_POST['title']) || empty($_POST['board_id']))
		fatal_lang_error('AR_empty_value', false);

	/* Cleaning! */
	$add['body'] = AutoRespondClean($_POST['body'], true);
	$add['title'] = AutoRespondClean($_POST['title']);
	$add['user'] = !empty($_POST['user_id']) ? (int) $_POST['user_id'] : 0;
	$add['board'] = implode(',', $_POST['board_id']);

	/* Insert! */
	$data = array(
		'board_id' => 'string',
		'user_id' => 'int',
		'title' => 'string',
		'body' => 'string'
	);
	$values = array(
		$add['board'],
		$add['user'],
		$add['title'],
		$add['body']
	);
	$indexes = array(
		'id'
	);
	$insert = new OharaDBClass('autorespond');
	$insert->InsertData($data, $values, $indexes);

	redirectexit('action=admin;area=autorespond;sa=list');
}


/* Load all boards */
function AutoRespondBoards()
{
	global $sourcedir, $modSettings;

	require_once($sourcedir . '/OharaDB.class.php');

	$board_params = array(
		'rows' =>'name, id_board',
		'where' => 'id_board != {int:recycle_board}',
		'order' => 'id_board',
	);
	$board_data = array(
		'recycle_board' => (int) $modSettings['recycle_board']
	);
	$board_query = new OharaDBClass('boards');
	$board_query->Params($board_params, $board_data);
	$board_query->GetData();

	return $board_query->data_result;
}

/* A function to cut-off text  */
function AutoRespondTruncate($string, $limit, $break = ' ', $pad ='...')
{
	/* return with no change if string is shorter than $limit */
	if(strlen($string) <= $limit)
		return $string;

	/* is $break present between $limit and the end of the string? */
	if(false !== ($breakpoint = strpos($string, $break, $limit)))
		if($breakpoint < strlen($string) - 1)
			$string = substr($string, 0, $breakpoint) . $pad;

	return $string;
}

/* Headers */
function AutoRespondHeaders()
{
	global $context, $modSettings;

	if(!empty($modSettings['AR_enable']) && $context['current_action'] == 'admin' && isset($_REQUEST['area']) && $_REQUEST['area'] == 'autorespond')
		$context['html_headers'] .= '
	<style type="text/css">
	/* Undo some styles from the master stylesheet */
	.checklist li { background: none; padding-left: 0; }

	/* CSS for checklists */
	.checklist {
		border: 1px solid #ccc;
		list-style: none;
		height: 10em;
		overflow: auto;
		width: 16em;
	}
	.checklist, .checklist li { margin-left: 0; padding: 0; }
	.checklist label { display: block; padding-left: 25px; text-indent: -25px; }
	.checklist label:hover, .checklist label.hover { background: #777; color: #fff; }
	* html .checklist label { height: 1%; }

	/* Checklist */
	.cl1 { font-size: 0.9em; width: 70%; height: 15em; }
	.cl1 .alt { background: #f5f5f5; }
	.cl1 input { vertical-align: middle; }
	.cl1 label:hover, .cl1 label.hover { background: #ddd; color: #000; }

	/* Form */
	.ar_form { width:90%; border-width:1px; border-color:#FFF;}
	.ar_form td { text-align:left;}
	/* Captation */
	#ar_captation {font-weight:bold;}
	.ar_table{
	background-attachment: scroll;
	background-clip: border-box;
	background-color: rgb(245, 245, 245);
	background-image: none;
	background-origin: padding-box;
	background-position: 0% 0%;
	background-repeat: repeat;
	background-size: auto;
	border-left-color: rgb(197, 197, 197);
	border-left-style: solid;
	border-left-width: 1px;
	border-right-color: rgb(197, 197, 197);
	border-right-style: solid;
	border-right-width: 1px;
	}
		</style>';
}
?>
