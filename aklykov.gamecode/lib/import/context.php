<?php
namespace Aklykov\Gamecode\Import;
use \Bitrix\Main\Localization\Loc as Loc;
Loc::loadMessages(__FILE__);

/**
 * Класс контекст выполнения логики импорта
 *
 * Class Context
 * @package Aklykov\Gamecode\Import
 */
class Context
{
	/**
	 * @var ImportStrategy
	 */
	private $importStrategy;

	/**
	 * @param ImportStrategy $strategy
	 */
	function __construct(ImportStrategy $strategy)
	{
		$this->importStrategy = $strategy;
	}

	function import()
	{
		$this->importStrategy->import();
	}

	function export()
	{
		$this->importStrategy->export();
	}
}