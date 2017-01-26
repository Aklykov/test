<?php
namespace Salerman\Kcm\Exception;

/**
 * Исключение при работе с "Поставками"
 *
 * Class SupplyImportException
 * @package Salerman\Kcm\Exception
 */
class SupplyImportException extends ImportException
{
	public function __construct($message, $errorLevel = 0, $errorFile = '', $errorLine = 0) {
		$this->message = $message;
		$this->file = $errorFile;
		$this->line = $errorLine;
	}

}