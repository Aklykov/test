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
			<th><?echo Loc::getMessage('AKLYKOV_USERGROUP_LIST_ID')?></th>
			<th><?echo Loc::getMessage('AKLYKOV_USERGROUP_LIST_NAME')?></th>
			<th><?echo Loc::getMessage('AKLYKOV_USERGROUP_LIST_DESCRIPTION')?></th>
		</tr>
		<?foreach ($arResult['ITEMS'] as $arItem) {?>
			<tr>
				<td><?=$arItem['ID']?></td>
				<td><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem['NAME']?></a></td>
				<td><?=$arItem['DESCRIPTION']?></td>
			</tr>
		<?}?>
	</table>
</section>