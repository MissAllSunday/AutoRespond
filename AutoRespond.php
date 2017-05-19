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

function AutoRespondAdmin(&$admin_areas)
{
	global $txt, $modSettings, $context;

	loadLanguage('AutoRespond');
	loadtemplate('AutoRespond');

	$admin_areas['config']['areas']['autorespond'] = array(
		'label' => $txt['AR_menu'],
		'file' => 'AutoRespond.php',
		'function' => 'ModifyAutoRespondSettings',
		'icon' => 'posts.gif',
		'subsections' => array(
			'basic' => array($txt['AR_basic_settings']),
			'list' => array($txt['AR_list_page']),
			'add' => array($txt['AR_admin_add']),
		),
	);
}

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

function BasicAutoRespondSettings($return_config = false)
{
	global $txt, $scripturl, $context, $sourcedir;

	/* I can has Adminz? */
	isAllowedTo('admin_forum');

	loadLanguage('AutoRespond');
	loadtemplate('AutoRespond');

	require_once($sourcedir . '/ManageServer.php');

	$config_vars = array(

		array('check', 'AR_enable', 'subtext' => $txt['AR_enable_sub']),
		array('check', 'AR_update_post_count', 'subtext' => $txt['AR_update_post_count_sub']),
		array('check', 'AR_use_title', 'subtext' => $txt['AR_use_title_sub']),
		array('check', 'AR_lock_topic_after', 'subtext' => $txt['AR_lock_topic_after_sub']),
		array('check', 'AR_dummy_ip', 'subtext' => $txt['AR_dummy_ip_sub']),

	);

	if ($return_config)
		return $config_vars;

	$context['post_url'] = $scripturl . '?action=admin;area=autorespond;save';
	$context['settings_title'] = $txt['AR_admin_panel'];
	$context['page_title'] = $txt['AR_admin_panel'];
	$context['sub_template'] = 'show_settings';

	if (isset($_GET['save']))
	{
		checkSession();
		saveDBSettings($config_vars);
		redirectexit('action=admin;area=autorespond');
	}

	prepareDBSettingContext($config_vars);
}

/* This will show a list of all your custom messages */
function AutoRespondListPage()
{
	global $txt, $context, $scripturl, $sourcedir;

	/* I can has Adminz? */
	isAllowedTo('admin_forum');
	loadLanguage('AutoRespond');
	loadtemplate('AutoRespond');

	require_once($sourcedir . '/OharaDB.class.php');


	/* Prepare the query */
	$params = array(
		'rows' =>'id, board_id, user_id, title, body',
		'order' => '{raw:sort}',
	);
	$data = array(
		'sort' => 'id',
	);
	$query = new OharaDBClass('autorespond');
	$query->Params($params, $data);
	$query->GetData('id');

	/* Store the result in context to handle it better */
	$context['GetARList'] = $query->data_result;

	/* Set some stuff for the page */
	$context['sub_template'] = 'auto_respond_list';
	$context['page_title'] = $txt['AR_admin_list'];
	$context['linktree'][] = array(
		'url' => $scripturl. '?action=admin;area=autorespond;sa=list',
		'name' => $txt['AR_admin_list'],
	);
}

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
function AutoRespondDelete() {

	global $sourcedir;

	/* I can has Adminz? */
	isAllowedTo('admin_forum');

	require_once($sourcedir . '/OharaDB.class.php');

	$validation = AutoRespondValidate();

	/* Safety first! */
	if (isset($_REQUEST['arid']) && in_array($_REQUEST['arid'], array_keys($validation)))
	{
		$params = array(
			'where' => 'id = {int:id}'
		);

		$data = array(
			'id' => $_REQUEST['arid']
		);
		$deletedata = new OharaDBClass('autorespond');
		$deletedata->Params($params, $data);
		$deletedata->DeleteData();

		redirectexit('action=admin;area=autorespond;sa=list');
	}
}

/* New stuff */
function AutoRespondAdd()
{
	global $txt, $context, $scripturl, $sourcedir;

	/* I can has Adminz? */
	isAllowedTo('admin_forum');
	loadLanguage('AutoRespond');
	loadtemplate('AutoRespond');
	AutoRespondHeaders();

	$context['sub_template'] = 'auto_respond_add';
	$context['page_title'] = $txt['AR_admin_adding'];
	$context['linktree'][] = array(
		'url' => $scripturl. '?action=admin;area=autorespond;sa=add',
		'name' => $txt['AR_admin_adding'],
	);

	/* This are empty...nobody knows why... (rolleyes) */
	$context['autorespond']['add'] = array(
		'board_id' => array(),
		'body' => '',
		'title' => '',
		'user_id' => '',
		'id' => ''
	);

	/* Load all boards */
	$context['autorespond']['boards'] = AutoRespondBoards();
}

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

/* The main function, this is where all the magic happens */
function AutoRespond($msgOptions, $topicOptions, $posterOptions){

	global $modSettings, $sourcedir, $context;

	/* Can't do much if the mod is not enable */
	if (empty($modSettings['AR_enable']))
		return;

	require_once($sourcedir . '/OharaDB.class.php');

	/* Get the message for this board */
	$params = array(
		'rows' =>'id, board_id, user_id, title, body',
		'where' => 'find_in_set("'.$topicOptions['board'].'",board_id) <> 0'
	);

	/* Prepare the query */
	$query = new OharaDBClass('autorespond');
	$query->Params($params);
	$query->GetData(null, true);

	/* There's no such thing... */
	if (empty($query->data_result))
		return;

	/* We need this... */
	require_once($sourcedir . '/Subs-Post.php');

	/* We got a message for this board */
	$context['AR_message'] = array(
		'id' => $query->data_result['id'],
		'user_id' => (int) $query->data_result['user_id'],
		'title' => $query->data_result['title'],
		'body' => un_preparsecode($query->data_result['body'])
	);

	/* Add in the default replacements. */
	$replacements = array(
		'TOPIC_POSTER' => $posterOptions['name'],
		'POSTED_TIME' => date("F j, Y, g:i a"),
		'TOPIC_SUBJECT' => $msgOptions['subject'],
	);

	/* Split the replacements up into two arrays, for use with str_replace */
	$find = array();
	$replace = array();

	foreach ($replacements as $f => $r)
	{
		$find[] = '{' . $f . '}';
		$replace[] = $r;
	}

	/* Do the variable replacements. */
	$context['AR_message']['body'] = str_replace($find, $replace, $context['AR_message']['body']);

	$newMsgOptions = array(
		'id' => 0,
		'subject' => !empty($modSettings['AR_use_title']) ? $context['AR_message']['title'] : $msgOptions['subject'],
		'body' => $context['AR_message']['body'],
		'icon' => 'xx',
		'smileys_enabled' => 1,
		'attachments' => array(),
	);

	$newTopicOptions = array(
		'id' => $topicOptions['id'],
		'board' => $topicOptions['board'],
		'poll' => null,
		'lock_mode' => !empty($modSettings['AR_lock_topic_after']) ? 1 : null,
		'sticky_mode' => null,
		'mark_as_read' => false,
	);

	$newPosterOptions = array(
		'id' => !empty($context['AR_message']['user_id']) ? $context['AR_message']['user_id'] : 1,
		'name' => '',
		'email' => '',
		'update_post_count' => !empty($modSettings['AR_update_post_count']) ? 1 : 0,
		'ip' => !empty($modSettings['AR_dummy_ip']) ? '127.0.0.1' : '',
	);

	/* Finally! */
	createPost($newMsgOptions, $newTopicOptions, $newPosterOptions);
}

/* DUH! WINNING! */
function AutoRespondWho()
{
	$MAS = '<a href="http://missallsunday.com" title="Free SMF Mods">Auto Respond mod &copy Suki</a>';

	return $MAS;
}

/* An extra query for validation purposes */
function AutoRespondValidate()
{
	global $sourcedir;

	require_once($sourcedir . '/OharaDB.class.php');

	/* Prepare the query */
	$params = array(
		'rows' =>'id'
	);
	$query = new OharaDBClass('autorespond');
	$query->Params($params);
	$query->GetData('id');

	$return = array();

	if (!empty($query->data_result))
		$return = $query->data_result;

	return $return;
}

/* Don't wash your dirty laundry in public... */
function AutoRespondClean($item, $body = false)
{
	global $smcFunc, $sourcedir;

	$item = $smcFunc['htmlspecialchars']($item, ENT_QUOTES);
	$item = $smcFunc['htmltrim']($item, ENT_QUOTES);
	$item = censorText($item);

	if ($body)
	{
		require_once($sourcedir . '/Subs-Post.php');
		preparsecode($item);
	}

	return $item;
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
