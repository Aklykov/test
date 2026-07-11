<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$APPLICATION->IncludeComponent(
	'bitrix:main.ui.filter',
	'',
	[
		'FILTER_ID' => $arResult['GRID_ID'],
		'GRID_ID' => $arResult['GRID_ID'],
		'FILTER' => $arResult['COLUMNS_FILTER'],
		'ENABLE_LIVE_SEARCH' => true,
		'ENABLE_LABEL' => true,
	]
);

$APPLICATION->IncludeComponent(
	'bitrix:main.ui.grid',
	'',
	[
		'GRID_ID' => $arResult['GRID_ID'],
		'COLUMNS' => $arResult['COLUMNS'],
		'ROWS' => $arResult['ROWS'],
		'SHOW_ROW_CHECKBOXES' => false,
		'SHOW_GRID_SETTINGS_MENU' => true,
		'SHOW_NAVIGATION_PANEL' => true,
		'SHOW_PAGINATION' => false,
		'SHOW_SELECTED_COUNTER' => false,
		'SHOW_TOTAL_COUNTER' => true,
		'ALLOW_COLUMNS_SORT' => true,
		'ALLOW_SORT' => true,
		'ALLOW_COLUMNS_RESIZE' => true,
		'ALLOW_HORIZONTAL_SCROLL' => true,
		'ALLOW_PIN_HEADER' => true,
		'AJAX_MODE' => 'Y',

		'NAV_OBJECT' => $arResult['NAV_OBJECT'],
		'SHOW_PAGINATION' => true,
		'SHOW_NAVIGATION_PANEL' => true,
		'SHOW_TOTAL_COUNTER' => false,
		'SHOW_PAGESIZE' => true,
		'DEFAULT_PAGE_SIZE' => 20,
		'PAGE_SIZES' => [
			['NAME' => '5', 'VALUE' => '5'],
			['NAME' => '10', 'VALUE' => '10'],
			['NAME' => '20', 'VALUE' => '20'],
			['NAME' => '50', 'VALUE' => '50'],
		],
	]
);