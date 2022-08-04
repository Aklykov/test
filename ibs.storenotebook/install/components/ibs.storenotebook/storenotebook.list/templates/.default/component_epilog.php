<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\UI\Extension;
Extension::load('ui.bootstrap4');

global $APPLICATION;
if (!empty($arResult['ITEMS'][0]['MANUFACTURER_NAME'])) {
	$APPLICATION->SetTitle($arResult['ITEMS'][0]['MANUFACTURER_NAME']);
} else if (!empty($arResult['ITEMS'][0]['MODEL_NAME'])) {
	$APPLICATION->SetTitle($arResult['ITEMS'][0]['MODEL_NAME']);
}

$nav = new \Bitrix\Main\UI\PageNavigation($arParams['GRID_ID']);
$nav->setPageSize($arParams['COUNT'])
	->initFromUri();

$nav->setRecordCount($arResult['ALL_COUNT']);

$APPLICATION->IncludeComponent(
	"bitrix:main.pagenavigation",
	"",
	array(
		"NAV_OBJECT" => $nav,
		"SEF_MODE" => "N",
		"SHOW_COUNT" => "Y",
	),
	false
);
