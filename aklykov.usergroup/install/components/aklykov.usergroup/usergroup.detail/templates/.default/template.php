<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
?>

<section class="usergroups">
	<table class="usergroups__table">
		<tr>
			<th><?echo Loc::getMessage('AKLYKOV_USERGROUP_DETAIL_ID')?></th>
			<th><?echo Loc::getMessage('AKLYKOV_USERGROUP_DETAIL_NAME')?></th>
			<th><?echo Loc::getMessage('AKLYKOV_USERGROUP_DETAIL_DESCRIPTION')?></th>
		</tr>
		<tr>
			<td><?=$arResult['ID']?></td>
			<td><?=$arResult['NAME']?></td>
			<td><?=$arResult['DESCRIPTION']?></td>
		</tr>
	</table>
	<br>
	<a href="<?=$arResult['FOLDER']?>"><?echo Loc::getMessage('AKLYKOV_USERGROUP_DETAIL_BACK')?></a>
</section>