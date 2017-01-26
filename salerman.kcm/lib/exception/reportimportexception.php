<?php
namespace Salerman\Kcm\Exception;

/**
 * Исключение при работе с "Отчеты агентов"
 *
 * Class ReportImportException
 * @package Salerman\Kcm\Exception
 */
class ReportImportException extends ImportException
{
	public function __construct($message, $errorLevel = 0, $errorFile = '', $errorLine = 0) {
		$this->message = $message;
		$this->file = $errorFile;
		$this->line = $errorLine;
	}

}