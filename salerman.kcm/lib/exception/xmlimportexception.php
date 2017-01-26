<?php
namespace Salerman\Kcm\Exception;

/**
 * Исключение проверка формата XML
 *
 * Class XmlImportException
 * @package Salerman\Kcm\Exception
 */
class XmlImportException extends ImportException
{
	public function __construct($message, $errorLevel = 0, $errorFile = '', $errorLine = 0) {
		$this->message = $message;
		$this->file = $errorFile;
		$this->line = $errorLine;
	}

}