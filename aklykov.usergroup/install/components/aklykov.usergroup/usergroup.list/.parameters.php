<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
/** @var array $arCurrentValues */
/** @global CUserTypeManager $USER_FIELD_MANAGER */

use Bitrix\Main\Localization\Loc;

$arComponentParameters = array(
	'PARAMETERS' => array(
		'CACHE_TIME' => array(
			'DEFAULT' => 36000000
		),
		'TITLE' => array(
			'NAME' => Loc::getMessage('AKLYKOV_USERGROUP_LIST_TITLE'),
			'TYPE' => 'STRING',
		),
	),
);
