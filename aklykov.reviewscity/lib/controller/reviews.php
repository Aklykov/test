<?php
namespace Aklykov\Reviewscity\Controller;

use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Engine\{Controller, ActionFilter};

class Reviews extends Controller
{
	public function configureActions(): array
	{
		return [
			'getList' => [
				'prefilters' => [
					new ActionFilter\Authentication(), // проверяет авторизован ли пользователь
				]
			]
		];
	}

	/**
	 * Получить список отзывов
	 *
	 * @param int $limit
	 * @param int $page
	 * @return array|array[]
	 * @throws \Bitrix\Main\ArgumentException
	 * @throws \Bitrix\Main\ArgumentNullException
	 * @throws \Bitrix\Main\ArgumentOutOfRangeException
	 * @throws \Bitrix\Main\LoaderException
	 * @throws \Bitrix\Main\ObjectPropertyException
	 * @throws \Bitrix\Main\SystemException
	 */
	public static function getListAction($limit=10, $page=1): array
	{
		Loader::includeModule('iblock');
		$result = [
			'list' => [],
			'all_count' => 0
		];

		$iblockId = Option::get('aklykov.reviewscity', 'IBLOCK_ID_REVIEWS', 0);
		if ($iblockId > 0)
		{
			$arItems = [];
			$arSectionIds = [];
			$rsElement = \CIBlockElement::GetList(
				[],
				[
					'IBLOCK_ID' => $iblockId,
					'ACTIVE' => 'Y'
				],
				false,
				[
					'nPageSize' => $limit,
					'iNumPage' => $page,
				],
				[
					'ID',
					'NAME',
					'DETAIL_TEXT',
					'PROPERTY_CITY',
					'PROPERTY_RATING',
				]
			);
			while ($arElement = $rsElement->GetNext())
			{
				$arItems[] = $arElement;
				$arSectionIds[] = $arElement['PROPERTY_CITY_VALUE'];
			}

			if (!empty($arSectionIds))
			{
				$rsSection = \Bitrix\Iblock\SectionTable::getList([
					'select' => ['ID', 'NAME'],
					'filter' => ['ID' => $arSectionIds],
				]);
				while ($arSection = $rsSection->fetch())
				{
					foreach ($arItems as &$arItem)
					{
						if ($arItem['PROPERTY_CITY_VALUE'] == $arSection['ID'])
							$arItem['CITY'] = $arSection['NAME'];
					}
					unset($arItem);
				}
			}

			foreach ($arItems as $arItem)
			{
				$result['list'][] = [
					'fields' => [
						'id' => $arItem['ID'],
						'name' => $arItem['NAME'],
						'text' => $arItem['DETAIL_TEXT'],
					],
					'properties' => [
						'city' => $arItem['CITY'],
						'rating' => $arItem['PROPERTY_RATING_VALUE'],
					],
				];
			}
			$result['all_count'] = $rsElement->NavRecordCount;
		}

		return $result;
	}
}