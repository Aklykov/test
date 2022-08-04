<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\UI\Extension;
Extension::load('ui.bootstrap4');

global $APPLICATION;
$APPLICATION->SetTitle($arResult['NAME']);