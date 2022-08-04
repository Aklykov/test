<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>

<?$APPLICATION->IncludeComponent(
	'ibs.storenotebook:storenotebook.list',
	'.default',
	array(
		'CACHE_TYPE' => $arParams['CACHE_TYPE'],
		'CACHE_TIME' => $arParams['CACHE_TIME'],
		'TYPE' => 'BRAND',
		'SORT_BY1' => $arParams['SORT_BY1'],
		'SORT_ORDER1' => $arParams['SORT_ORDER1'],
		'SORT_BY2' => $arParams['SORT_BY2'],
		'SORT_ORDER2' => $arParams['SORT_ORDER2'],
		'COUNT' => $arResult['COUNT'],
		'PAGE' => $arResult['PAGE'],
		'GRID_ID' => $arResult['GRID_ID'],
		'BRAND' => $arResult['VARIABLES']['BRAND'],
		'MODEL' => $arResult['VARIABLES']['MODEL'],
		'DETAIL_URL_TEMPLATE' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['list_model'],
	),
	false
);?>
