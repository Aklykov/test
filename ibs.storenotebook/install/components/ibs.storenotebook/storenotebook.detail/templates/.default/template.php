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

<div class="card">
	<div class="card-body">
		<h5 class="card-title"><?=$arResult['NAME']?></h5>
		<p class="card-text">
			<strong><?=Loc::getMessage('IBS_STORENOTEBOOK_DETAIL_NAME')?>:</strong> <?=$arResult['NAME']?><br>
			<strong><?=Loc::getMessage('IBS_STORENOTEBOOK_DETAIL_YEAR')?>:</strong> <?=$arResult['YEAR']?><br>
			<strong><?=Loc::getMessage('IBS_STORENOTEBOOK_DETAIL_PRICE')?>:</strong> <?=$arResult['PRICE']?><br>
			<strong><?=Loc::getMessage('IBS_STORENOTEBOOK_DETAIL_OPTIONS')?>:</strong> <?=implode(', ', $arResult['OPTIONS'])?><br>
		</p>
	</div>
</div>