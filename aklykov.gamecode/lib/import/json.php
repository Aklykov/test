<?php
namespace Aklykov\Gamecode\Import;
use \Bitrix\Main\Localization\Loc as Loc;
Loc::loadMessages(__FILE__);

/**
 * Класс реализует стратегию работы с файлом типа Json
 *
 * Class Json
 * @package Aklykov\Gamecode\Import
 */
class Json implements ImportStrategy
{

	function import()
	{
		global $APPLICATION;
		$APPLICATION->ThrowException(Loc::getMessage('AKLYKOV_GAMECODE_IMPORT_JSON_IMPORT'));
	}

	function export()
	{
		global $APPLICATION;
		$APPLICATION->ThrowException(Loc::getMessage('AKLYKOV_GAMECODE_IMPORT_JSON_EXPORT'));
	}

}