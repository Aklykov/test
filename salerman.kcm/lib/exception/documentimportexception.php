<?php
namespace Salerman\Kcm\Exception;

/**
 * Исключение проверок документа
 *
 * Class DocumentImportException
 * @package Salerman\Kcm\Exception
 */
class DocumentImportException extends ImportException
{
	public function __construct($message, $errorLevel = 0, $errorFile = '', $errorLine = 0) {
		$this->message = $message;
		$this->file = $errorFile;
		$this->line = $errorLine;
	}

}