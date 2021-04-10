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
		'GROUP_ID' => array(
			'NAME' => Loc::getMessage('AKLYKOV_USERGROUP_DETAIL_GROUP_ID'),
			'TYPE' => 'STRING',
		),
	),
);
