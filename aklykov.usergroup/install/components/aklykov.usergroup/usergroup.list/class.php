<?php
namespace Aklykov\UserGroup;

use Bitrix\Main,
	Bitrix\Main\Loader,
	Bitrix\Main\Localization\Loc;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);

class UsergroupListComponent extends \CBitrixComponent
{
	public function getTitle()
	{
		return $this->arParams['TITLE'];
	}

	public function getGroups()
	{
		$detailUrlTamplate = $this->arParams['DETAIL_URL_TEMPLATE'];

		$groups = [];
		$rsGroup = \Bitrix\Main\GroupTable::getList([
			'order' => ['NAME' => 'ASC'],
			'filter' => ['ACTIVE' => 'Y'],
			'select' => ['ID', 'NAME', 'DESCRIPTION'],
		]);
		while ($group = $rsGroup->fetch())
		{
			$group['DETAIL_PAGE_URL'] = str_replace('#GROUP_ID#', $group['ID'], $detailUrlTamplate);
			$groups[] = $group;
		}

		return $groups;
	}

	public function executeComponent()
	{
		global $APPLICATION;
		$APPLICATION->SetTitle($this->getTitle());

		if ($this->startResultCache($this->arParams['CACHE_TIME']))
		{
			$this->arResult['TITLE'] = $this->getTitle();
			$this->arResult['ITEMS'] = $this->getGroups();

			$this->includeComponentTemplate();
		}
	}
}
