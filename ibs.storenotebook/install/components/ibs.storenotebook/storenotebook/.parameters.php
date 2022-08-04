<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
/** @var array $arCurrentValues */
/** @global CUserTypeManager $USER_FIELD_MANAGER */

use Bitrix\Main\Localization\Loc;

$arSortFields = [
	'PRICE' => Loc::getMessage('IBS_STORENOTEBOOK_LIST_SORT_PRICE'),
	'YEAR' => Loc::getMessage('IBS_STORENOTEBOOK_LIST_SORT_YEAR'),
];
$arSorts = [
	'ASC' => Loc::getMessage('IBS_STORENOTEBOOK_LIST_SORT_ASC'),
	'DESC' => Loc::getMessage('IBS_STORENOTEBOOK_LIST_SORT_DESC'),
];

$arComponentParameters = array(
	'PARAMETERS' => array(
		'CACHE_TIME' => array(
			'DEFAULT' => 36000000
		),
		'SORT_BY1' => array(
			'NAME' => Loc::getMessage('IBS_STORENOTEBOOK_LIST_SORT_BY1'),
			'TYPE' => 'LIST',
			'VALUES' => $arSortFields,
		),
		'SORT_ORDER1' => array(
			'NAME' => Loc::getMessage('IBS_STORENOTEBOOK_LIST_SORT_ORDER1'),
			'TYPE' => 'LIST',
			'VALUES' => $arSorts,
		),
		'SORT_BY2' => array(
			'NAME' => Loc::getMessage('IBS_STORENOTEBOOK_LIST_SORT_BY2'),
			'TYPE' => 'LIST',
			'VALUES' => $arSortFields,
		),
		'SORT_ORDER2' => array(
			'NAME' => Loc::getMessage('IBS_STORENOTEBOOK_LIST_SORT_ORDER2'),
			'TYPE' => 'LIST',
			'VALUES' => $arSorts,
		),
		'COUNT' => array(
			'NAME' => Loc::getMessage('IBS_STORENOTEBOOK_LIST_COUNT'),
			'TYPE' => 'STRING',
			'DEFAULT' => '2',
		),
		'PAGE' => array(
			'NAME' => Loc::getMessage('IBS_STORENOTEBOOK_LIST_PAGE'),
			'TYPE' => 'STRING',
			'DEFAULT' => '1',
		),
		'VARIABLE_ALIASES' => array(
			'BRAND' => array('NAME' => Loc::getMessage('IBS_STORENOTEBOOK_BRAND')),
			'MODEL' => array('NAME' => Loc::getMessage('IBS_STORENOTEBOOK_MODEL')),
			'NOTEBOOK' => array('NAME' => Loc::getMessage('IBS_STORENOTEBOOK_NOTEBOOK')),
		),
		'SEF_MODE' => array(
			'list_brand' => array(
				'NAME' => Loc::getMessage('IBS_STORENOTEBOOK_LIST_BRAND'),
				'DEFAULT' => '',
				'VARIABLES' => array(
				),
			),
			'list_model' => array(
				'NAME' => Loc::getMessage('IBS_STORENOTEBOOK_LIST_MODEL'),
				'DEFAULT' => '#BRAND#/',
				'VARIABLES' => array(
					'BRAND',
				),
			),
			'list_notebook' => array(
				'NAME' => Loc::getMessage('IBS_STORENOTEBOOK_LIST_NOTEBOOK'),
				'DEFAULT' => '#BRAND#/#MODEL#/',
				'VARIABLES' => array(
					'BRAND',
					'MODEL',
				),
			),
			'detail_notebook' => array(
				'NAME' => Loc::getMessage('IBS_STORENOTEBOOK_DETAIL_NOTEBOOK'),
				'DEFAULT' => '#BRAND#/#MODEL#/detail/#NOTEBOOK#/',
				'VARIABLES' => array(
					'BRAND',
					'MODEL',
					'NOTEBOOK',
				),
			),
		),
	),
);

if ($arCurrentValues['SEF_MODE']=='Y')
{
	$arComponentParameters['PARAMETERS']['VARIABLE_ALIASES'] = array();
	$arComponentParameters['PARAMETERS']['VARIABLE_ALIASES']['BRAND'] = array(
		'NAME' => Loc::getMessage('IBS_STORENOTEBOOK_BRAND'),
		'TEMPLATE' => '#BRAND#',
	);
	$arComponentParameters['PARAMETERS']['VARIABLE_ALIASES']['MODEL'] = array(
		'NAME' => Loc::getMessage('IBS_STORENOTEBOOK_MODEL'),
		'TEMPLATE' => '#MODEL#',
	);
	$arComponentParameters['PARAMETERS']['VARIABLE_ALIASES']['NOTEBOOK'] = array(
		'NAME' => Loc::getMessage('IBS_STORENOTEBOOK_NOTEBOOK'),
		'TEMPLATE' => '#NOTEBOOK#',
	);
}
