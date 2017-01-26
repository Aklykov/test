<?php
namespace Aklykov\Gamecode\Import;
use \Bitrix\Main\Localization\Loc as Loc;
Loc::loadMessages(__FILE__);

/**
 * Интерфейс классов-стратегий для импорта
 *
 * Class ImportStrategy
 * @package Aklykov\Gamecode\Import
 */
interface ImportStrategy
{

	/**
	 * Импортировать данные из файла в БД
	 *
	 */
	function import();

	/**
	 * Экспортировать данные из файла в БД
	 *
	 */
	function export();

}