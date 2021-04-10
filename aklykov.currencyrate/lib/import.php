<?php
namespace Aklykov\Currencyrate;
use \Bitrix\Main\Type\DateTime;

class Import
{
	public static function loadFromCBR()
	{
		$url = 'http://www.cbr.ru/scripts/XML_daily.asp';
		$xml = \simplexml_load_file($url);
		$attributes = [];
		foreach ($xml->attributes() as $key => $value)
		{
			$attributes[$key] = (string) $value;
		}

		foreach ($xml->Valute as $xmlValute)
		{
			$code = (string) $xmlValute->CharCode;
			$course = (float) str_replace(',', '.', (string) $xmlValute->Value);
			$course = round($course, 2);

			$result = CurrencyrateTable::add([
				'CODE' => $code,
				'DATE_CREATE' => \Bitrix\Main\Type\DateTime::createFromTimestamp(strtotime($attributes['Date'])),
				'COURSE' => $course,
			]);
		}

		return '\Aklykov\Currencyrate\Import::loadFromCBR();';
	}
}