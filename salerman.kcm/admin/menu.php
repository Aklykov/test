<?php
IncludeModuleLangFile(__FILE__);

if(!CModule::IncludeModule('salerman.kcm'))
{
	return false;
}

$items = array();
$items[] = array(
	"text" => "Список договоров",
	"url" => "/bitrix/admin/salerman_documents_list.php?lang=".LANG,
	"module_id" => "salerman.kcm",
	"more_url" => Array(
		"/bitrix/admin/salerman_documents_list.php?lang=".LANG,
		"/bitrix/admin/salerman_documents_edit.php?lang=".LANG
	),
);

return array(
	"parent_menu" => "global_menu_content",
	"section" => "salerman.kcm",
	"sort" => 150,
	"text" => "Красцветмет",
	"url" => "",
	"icon" => "iblock_menu_icon_types",
	"page_icon" => "highloadblock_page_icon",
	"more_url" => array(
		"salerman_documents_list.php",
		"salerman_documents_edit.php"
	),
	"items_id" => "menu_salerman.kcm",
	"items" => $items
);
