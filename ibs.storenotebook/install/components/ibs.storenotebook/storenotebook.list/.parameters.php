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
		'TYPE' => array(
			'NAME' => Loc::getMessage('IBS_STORENOTEBOOK_LIST_TYPE'),
			'TYPE' => 'LIST',
			'VALUES' => [
				'BRAND' => Loc::getMessage('IBS_STORENOTEBOOK_LIST_TYPE_BRAND'),
				'MODEL' => Loc::getMessage('IBS_STORENOTEBOOK_LIST_TYPE_MODEL'),
				'NOTEBOOK' => Loc::getMessage('IBS_STORENOTEBOOK_LIST_TYPE_NOTEBOOK'),
			],
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
		'GRID_ID' => array(
			'NAME' => Loc::getMessage('IBS_STORENOTEBOOK_LIST_GRID_ID'),
			'TYPE' => 'STRING',
		),
		'BRAND' => array(
			'NAME' => Loc::getMessage('IBS_STORENOTEBOOK_LIST_BRAND'),
			'TYPE' => 'STRING',
		),
		'MODEL' => array(
			'NAME' => Loc::getMessage('IBS_STORENOTEBOOK_LIST_MODEL'),
			'TYPE' => 'STRING',
		),
	),
);
