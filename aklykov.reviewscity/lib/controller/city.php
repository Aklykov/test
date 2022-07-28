<?php
namespace Aklykov\Reviewscity\Controller;

use Bitrix\Main\Loader;
use Bitrix\Main\Engine\{Controller, ActionFilter};

class City extends Controller
{
	public function configureActions(): array
	{
		return [
			'getListIblockAndSections' => [
				'prefilters' => [
					new ActionFilter\Authentication(), // проверяет авторизован ли пользователь
				]
			]
		];
	}

	/**
	 * Получить список ИБ и Разделов
	 *
	 * @return array
	 * @throws \Bitrix\Main\ArgumentException
	 * @throws \Bitrix\Main\LoaderException
	 * @throws \Bitrix\Main\ObjectPropertyException
	 * @throws \Bitrix\Main\SystemException
	 */
	public static function getListIblockAndSectionsAction(): array
	{
		Loader::includeModule('iblock');

		$result = [];
		$rsIblock = \Bitrix\Iblock\IblockTable::getList([
			'order' => ['NAME' => 'ASC'],
			'select' => ['ID', 'NAME'],
			'filter' => ['ACTIVE' => 'Y'],
		]);
		while ($arIblock = $rsIblock->fetch())
		{
			$result[$arIblock['ID']] = [
				'ID' => $arIblock['ID'],
				'NAME' => $arIblock['NAME'],
				'SECTIONS' => []
			];
		}

		$rsSection = \Bitrix\Iblock\SectionTable::getList([
			'order' => ['NAME' => 'ASC'],
			'select' => ['ID', 'NAME', 'IBLOCK_ID'],
			'filter' => ['ACTIVE' => 'Y'],
		]);
		while ($arSection = $rsSection->fetch())
		{
			$result[$arSection['IBLOCK_ID']]['SECTIONS'][] = [
				'ID' => $arSection['ID'],
				'NAME' => $arSection['NAME']
			];
		}

		$result = array_filter($result, function($iblock){
			return !empty($iblock['SECTIONS']);
		});

		$result = array_values($result);

		return $result;
	}
}