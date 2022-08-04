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
		'NOTEBOOK' => array(
			'NAME' => Loc::getMessage('IBS_STORENOTEBOOK_DETAIL_NOTEBOOK'),
			'TYPE' => 'STRING',
		),
	),
);
