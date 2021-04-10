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
			'NAME' => Loc::getMessage('AKLYKOV_USERGROUP_TITLE'),
			'TYPE' => 'STRING',
		),
		'VARIABLE_ALIASES' => array(
			'GROUP_ID' => array(
				'NAME' => Loc::getMessage('AKLYKOV_USERGROUP_GROUP_ID'),
			),
		),
		'SEF_MODE' => array(
			'list' => array(
				'NAME' => Loc::getMessage('AKLYKOV_USERGROUP_LIST'),
				'DEFAULT' => '',
				'VARIABLES' => array(
				),
			),
			'detail' => array(
				'NAME' => Loc::getMessage('AKLYKOV_USERGROUP_DETAIL'),
				'DEFAULT' => '#GROUP_ID#/',
				'VARIABLES' => array(
					'GROUP_ID',
					'GROUP_CODE',
				),
			),
		),
	),
);

if ($arCurrentValues['SEF_MODE']=='Y')
{
	$arComponentParameters['PARAMETERS']['VARIABLE_ALIASES'] = array();
	$arComponentParameters['PARAMETERS']['VARIABLE_ALIASES']['GROUP_ID'] = array(
		'NAME' => Loc::getMessage('AKLYKOV_USERGROUP_GROUP_ID'),
		'TEMPLATE' => '#GROUP_ID#',
	);
	$arComponentParameters['PARAMETERS']['VARIABLE_ALIASES']['GROUP_CODE'] = array(
		'NAME' => Loc::getMessage('AKLYKOV_USERGROUP_GROUP_CODE'),
		'TEMPLATE' => '#GROUP_CODE#',
	);
}
