<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc;

$arComponentDescription = array(
	'NAME' => Loc::getMessage('AKLYKOV_USERGROUP_DETAIL_NAME'),
	'DESCRIPTION' => Loc::getMessage('AKLYKOV_USERGROUP_DETAIL_DESCRIPTION'),
	'COMPLEX' => 'N',
	'SORT' => 10,
	'PATH' => array(
		'ID' => 'content',
		'CHILD' => array(
			'ID' => 'aklykov.usergroup',
			'NAME' => 'aklykov.usergroup'
		)
	),
);
