<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>

<?$APPLICATION->IncludeComponent(
	"aklykov.usergroup:usergroup.detail",
	".default",
	array(
		"CACHE_TYPE" => $arParams['CACHE_TYPE'],
		"CACHE_TIME" => $arParams['CACHE_TIME'],
		"GROUP_ID" => $arResult['VARIABLES']['GROUP_ID'],
		"FOLDER" => $arResult['FOLDER'],
		"COMPONENT_TEMPLATE" => ".default",
	),
	false
);?>