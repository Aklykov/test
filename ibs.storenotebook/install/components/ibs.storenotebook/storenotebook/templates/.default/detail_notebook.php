<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>

<?$APPLICATION->IncludeComponent(
	"ibs.storenotebook:storenotebook.detail",
	".default",
	array(
		"CACHE_TYPE" => $arParams['CACHE_TYPE'],
		"CACHE_TIME" => $arParams['CACHE_TIME'],
		"NOTEBOOK" => $arResult['VARIABLES']['NOTEBOOK'],
		"COMPONENT_TEMPLATE" => ".default",
	),
	false
);?>