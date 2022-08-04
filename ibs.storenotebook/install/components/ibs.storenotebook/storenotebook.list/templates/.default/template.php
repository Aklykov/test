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

if (empty($arResult['ITEMS']))
	return;
?>

<ul class="list-group">
	<?foreach ($arResult['ITEMS'] as $arItem) {?>
		<li class="list-group-item">
			<a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem['NAME']?></a>
			<?if (!empty($arItem['YEAR'])):?>
				<br><?=Loc::getMessage('IBS_STORENOTEBOOK_LIST_YEAR')?>: <?=$arItem['YEAR']?>
			<?endif;?>
			<?if (!empty($arItem['PRICE'])):?>
				<br><?=Loc::getMessage('IBS_STORENOTEBOOK_LIST_PRICE')?>: <?=$arItem['PRICE']?>
			<?endif;?>
		</li>
	<?}?>
</ul>
