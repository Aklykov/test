<?php
namespace Aklykov\Gamecode\Import;
use \Bitrix\Main\Config\Option as Option;
use \Aklykov\Gamecode\Gamecode as Gamecode;
use \Bitrix\Main\Localization\Loc as Loc;
Loc::loadMessages(__FILE__);

/**
 * Класс реализует стратегию работы с файлом типа Csv
 *
 * Class Csv
 * @package Aklykov\Gamecode\Import
 */
class Csv implements ImportStrategy
{

	const DELIMITER = ';';

	function import()
	{
		// Получаем путь к файлу
		$path_from = Option::get(Gamecode::MODULE_ID, 'path_from');
		if(file_exists($_SERVER['DOCUMENT_ROOT'] . $path_from))
		{
			$b_firstRow = true;
			$handle = fopen($_SERVER['DOCUMENT_ROOT'] . $path_from, "r");
			while(($data = fgetcsv($handle, 1000, self::DELIMITER)) !== FALSE)
			{
				if($b_firstRow)
				{
					$b_firstRow = false;
					continue;
				}
				// Сохраняем в БД
				$obj = new Gamecode();
				$obj->code = $data[0];
				$obj->order = $data[1];
				$obj->email = $data[2];
				$obj->save();
			}
			fclose($handle);
		}
		else
		{
			global $APPLICATION;
			$APPLICATION->ThrowException(Loc::getMessage('AKLYKOV_GAMECODE_IMPORT_CSV_DONT_FOUND'));
		}
	}

	function export()
	{
		// Получаем путь куда выгружать
		$path_to = Option::get(Gamecode::MODULE_ID, 'path_to');
		$handle = fopen($_SERVER['DOCUMENT_ROOT'] . $path_to, "w");
		// Записываем заголовок
		fputcsv($handle, array('Серийный номер', 'Номер заказа', 'Email заказа'), self::DELIMITER);
		// Запрашиваем записи и записываем их в файл
		$arObj = Gamecode::getList();
		foreach($arObj as $obj)
		{
			fputcsv($handle, array($obj->code, $obj->order, $obj->email), self::DELIMITER);
		}
		fclose($handle);
	}

}