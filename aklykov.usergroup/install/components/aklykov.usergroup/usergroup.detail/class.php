<?php
namespace Aklykov\UserGroup;

use Bitrix\Main,
	Bitrix\Main\Loader,
	Bitrix\Main\Localization\Loc;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);

class UsergroupListComponent extends \CBitrixComponent
{
	protected function getFolder()
	{
		return $this->arParams['FOLDER'];
	}

	protected function getGroup()
	{
		$group = [];
		$rsGroup = \Bitrix\Main\GroupTable::getList([
			'order' => ['NAME' => 'ASC'],
			'filter' => [
				'ACTIVE' => 'Y',
				'ID' => $this->arParams['GROUP_ID']
			],
			'select' => ['ID', 'NAME', 'DESCRIPTION'],
			'limit' => 1
		]);
		if ($group = $rsGroup->fetch())
		{
			return $group;
		}
		else
		{
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
		if ($this->startResultCache($this->arParams['CACHE_TIME']))
		{
			$this->arResult = $this->getGroup();
			$this->arResult['FOLDER'] = $this->getFolder();

			$this->includeComponentTemplate();
		}
	}
}
