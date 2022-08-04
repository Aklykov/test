<?php
namespace Ibs\StoreNotebook;

use Bitrix\Main,
	Bitrix\Main\Loader,
	Bitrix\Main\Localization\Loc;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);

class StoreNotebookDetailComponent extends \CBitrixComponent
{
	protected function getFolder()
	{
		return $this->arParams['FOLDER'];
	}

	protected function getItem()
	{
		$item = [];
		$rsItem = Entity\NotebookTable::getList([
			'filter' => [
				'ID' => $this->arParams['NOTEBOOK']
			],
			'select' => ['*'],
			'limit' => 1
		]);
		if ($item = $rsItem->fetch()) {
			// дозапрашиваем опции
			$rsOption = Entity\OptionLinkNotebookTable::getList([
				'filter' => [
					'NOTEBOOK_ID' => $item['ID']
				],
				'select' => ['OPTION.NAME']
			]);
			while ($option = $rsOption->fetch()) {
				$item['OPTIONS'][] = $option['IBS_STORENOTEBOOK_ENTITY_OPTION_LINK_NOTEBOOK_OPTION_NAME'];
			}

			return $item;
		} else {
			// Выдаем 404
			\Bitrix\Main\Loader::includeModule('iblock');
			\Bitrix\Iblock\Component\Tools::process404(
				'',
				true,
				true,
				true
			);
			return;
		}
	}

	public function executeComponent()
	{
		\Bitrix\Main\Loader::includeModule('ibs.storenotebook');

		if ($this->startResultCache($this->arParams['CACHE_TIME'])) {
			$this->arResult = $this->getItem();

			$this->includeComponentTemplate();
		}
	}
}
