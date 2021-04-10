<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>

<?$APPLICATION->IncludeComponent(
	"aklykov.usergroup:usergroup.list",
	".default",
	array(
		"CACHE_TYPE" => $arParams['CACHE_TYPE'],
		"CACHE_TIME" => $arParams['CACHE_TIME'],
		"COMPONENT_TEMPLATE" => ".default",
		"TITLE" => $arParams['TITLE'],
		"DETAIL_URL_TEMPLATE" => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['detail'],
	),
	false
);?>
