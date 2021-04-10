<?php
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Loader;
Loc::loadMessages(__FILE__);

if (!Loader::includeModule('aklykov.currencyrate'))
	return false;

$items = array();
$items[] = array(
	'text' => Loc::getMessage('AKLYKOV_CURRENCYRATE_MENU_LIST'),
	'url' => '/bitrix/admin/aklykov_currencyrate_list.php?lang='.LANG,
	'module_id' => 'aklykov.currencyrate',
	'more_url' => Array(
		'/bitrix/admin/aklykov_currencyrate_list.php?lang='.LANG,
		'/bitrix/admin/aklykov_currencyrate_edit.php?lang='.LANG
	),
);

return array(
	'parent_menu' => 'global_menu_content',
	'section' => 'aklykov.currencyrate',
	'sort' => 150,
	'text' => Loc::getMessage('AKLYKOV_CURRENCYRATE_MENU_TITLE'),
	'url' => '',
	'icon' => 'iblock_menu_icon_types',
	'page_icon' => 'highloadblock_page_icon',
	'more_url' => array(
		'aklykov_currencyrate_list.php',
		'aklykov_currencyrate_edit.php'
	),
	'items_id' => 'menu_aklykov.currencyrate',
	'items' => $items
);
