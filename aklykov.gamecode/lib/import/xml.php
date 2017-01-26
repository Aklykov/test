<?php
namespace Aklykov\Gamecode\Import;
use \Bitrix\Main\Localization\Loc as Loc;
Loc::loadMessages(__FILE__);

/**
 * Класс реализует стратегию работы с файлом типа Xml
 *
 * Class Xml
 * @package Aklykov\Gamecode\Import
 */
class Xml implements ImportStrategy
{

	function import()
	{
		global $APPLICATION;
		$APPLICATION->ThrowException(Loc::getMessage('AKLYKOV_GAMECODE_IMPORT_XML_IMPORT'));
	}

	function export()
	{
		global $APPLICATION;
		$APPLICATION->ThrowException(Loc::getMessage('AKLYKOV_GAMECODE_IMPORT_XML_EXPORT'));
	}

}