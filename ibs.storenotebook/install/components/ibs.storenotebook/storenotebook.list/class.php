<?php
namespace Ibs\StoreNotebook;

use Bitrix\Main,
	Bitrix\Main\Loader,
	Bitrix\Main\Localization\Loc;

use Bitrix\Main\Grid\Options as GridOptions;
use Bitrix\Main\UI\PageNavigation;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);

class StoreNotebookListComponent extends \CBitrixComponent
{
	public function getBrands()
	{
		$items = [];
		$rsItem = Entity\ManufacturerTable::getList([
			'order' => ['NAME' => 'ASC'],
			'select' => ['*'],
			'limit' => $this->arParams['COUNT'],
			'offset' => $this->arParams['COUNT'] * ($this->arParams['PAGE'] - 1),
			'count_total' => true,
		]);
		while ($item = $rsItem->fetch()) {
			$item['DETAIL_PAGE_URL'] = str_replace('#BRAND#', $item['ID'], $this->arParams['DETAIL_URL_TEMPLATE']);
			$items[] = $item;
		}

		$this->arResult['ALL_COUNT'] = $rsItem->getCount();

		return $items;
	}

	public function getModels()
	{
		$items = [];
		$rsItem = Entity\ModelTable::getList([
			'order' => ['NAME' => 'ASC'],
			'select' => ['*', 'MANUFACTURER.NAME'],
			'limit' => $this->arParams['COUNT'],
			'offset' => $this->arParams['COUNT'] * ($this->arParams['PAGE'] - 1),
			'filter' => ['MANUFACTURER_ID' => $this->arParams['BRAND']],
			'count_total' => true,
		]);
		while ($item = $rsItem->fetch()) {
			$item['MANUFACTURER_NAME'] = $item['IBS_STORENOTEBOOK_ENTITY_MODEL_MANUFACTURER_NAME'];

			$item['DETAIL_PAGE_URL'] = str_replace('#BRAND#', $item['MANUFACTURER_ID'], $this->arParams['DETAIL_URL_TEMPLATE']);
			$item['DETAIL_PAGE_URL'] = str_replace('#MODEL#', $item['ID'], $item['DETAIL_PAGE_URL']);
			$items[] = $item;
		}

		$this->arResult['ALL_COUNT'] = $rsItem->getCount();

		return $items;
	}

	public function getNotebook()
	{
		$items = [];
		$rsItem = Entity\NotebookTable::getList([
			'order' => [
				$this->arParams['SORT_BY1'] => $this->arParams['SORT_ORDER1'],
				$this->arParams['SORT_BY2'] => $this->arParams['SORT_ORDER2'],
			],
			'select' => ['*', 'MODEL.MANUFACTURER.ID', 'MODEL.NAME'],
			'limit' => $this->arParams['COUNT'],
			'offset' => $this->arParams['COUNT'] * ($this->arParams['PAGE'] - 1),
			'filter' => [
				'MODEL_ID' => $this->arParams['MODEL']
			],
			'count_total' => true,
		]);

		while ($item = $rsItem->fetch()) {
			$item['MODEL_NAME'] = $item['IBS_STORENOTEBOOK_ENTITY_NOTEBOOK_MODEL_NAME'];
			$item['MANUFACTURER_ID'] = $item['IBS_STORENOTEBOOK_ENTITY_NOTEBOOK_MODEL_MANUFACTURER_ID'];
			$item['DETAIL_PAGE_URL'] = str_replace('#BRAND#', $item['MANUFACTURER_ID'], $this->arParams['DETAIL_URL_TEMPLATE']);
			$item['DETAIL_PAGE_URL'] = str_replace('#MODEL#', $item['MODEL_ID'], $item['DETAIL_PAGE_URL']);
			$item['DETAIL_PAGE_URL'] = str_replace('#NOTEBOOK#', $item['ID'], $item['DETAIL_PAGE_URL']);
			$items[] = $item;
		}

		$this->arResult['ALL_COUNT'] = $rsItem->getCount();

		return $items;
	}

	public function executeComponent()
	{
		global $APPLICATION;
		\Bitrix\Main\Loader::includeModule('ibs.storenotebook');

		if ($this->startResultCache($this->arParams['CACHE_TIME'])) {
			switch ($this->arParams['TYPE']) {
				case 'BRAND':
					$this->arResult['ITEMS'] = $this->getBrands();
					break;
				case 'MODEL':
					$this->arResult['ITEMS'] = $this->getModels();
					break;
				case 'NOTEBOOK':
					$this->arResult['ITEMS'] = $this->getNotebook();
					break;
			}

			$this->includeComponentTemplate();
		}
	}
}
