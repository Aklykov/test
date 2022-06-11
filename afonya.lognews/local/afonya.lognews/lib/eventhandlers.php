<?php
namespace Afonya\Lognews;

use Bitrix\Main\{Config\Option, Localization\Loc};
Loc::loadMessages(__FILE__);

/**
 * Class EventHandlers
 *
 * @package \Afonya\Lognews
 **/
class EventHandlers
{
	/**
	 * Обработчик события добавления новости
	 *
	 * @param array $arFields
	 */
	public static function onAfterIBlockElementAdd(&$arFields=[])
	{
		$iblockId = Option::get('afonya.lognews', 'UF_IBLOCK_ID_NEWS');
		if ($arFields['IBLOCK_ID'] == $iblockId) {
			$newsId = $arFields['ID'];

			if ($newsId > 0)
				Entity\LogTable::createEventAdd($newsId);
		}
	}

	/**
	 * Обработчик события изменения новости
	 *
	 * @param array $arFields
	 */
	public static function onAfterIBlockElementUpdate(&$arFields=[])
	{
		$iblockId = Option::get('afonya.lognews', 'UF_IBLOCK_ID_NEWS');
		if ($arFields['IBLOCK_ID'] == $iblockId) {
			$newsId = $arFields['ID'];

			if ($newsId > 0)
				Entity\LogTable::createEventUpdate($newsId);
		}
	}

	/**
	 * Обработчик события удаление новости
	 *
	 * @param array $arFields
	 */
	public static function onAfterIBlockElementDelete($arFields=[])
	{
		$iblockId = Option::get('afonya.lognews', 'UF_IBLOCK_ID_NEWS');
		if ($arFields['IBLOCK_ID'] == $iblockId) {
			$newsId = $arFields['ID'];

			if ($newsId > 0)
				Entity\LogTable::createEventDelete($newsId);
		}
	}
}