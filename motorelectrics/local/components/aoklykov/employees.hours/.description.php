<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arComponentDescription = [
	'NAME' => Loc::getMessage('AOKLYKOV_EMPLOYEE_HOURS_NAME'),
	'DESCRIPTION' => Loc::getMessage('AOKLYKOV_EMPLOYEE_HOURS_DESCRIPTION'),
	'ICON' => '',
	'SORT' => 100,
	'CACHE_PATH' => 'Y',
	'PATH' => [
		'ID' => 'aoklykov',
		'NAME' => Loc::getMessage('AOKLYKOV_EMPLOYEE_HOURS_PATH_NAME'),
		'CHILD' => [
			'ID' => 'employees',
			'NAME' => Loc::getMessage('AOKLYKOV_EMPLOYEE_HOURS_PATH_CHILD_NAME'),
		],
	],
];