<?php
namespace Salerman\Kcm\Exception;

/**
 * Исключения импорта
 *
 * Class ImportException
 * @package Salerman\Kcm\Exception
 */
class ImportException extends \Exception
{
	public function __construct($message, $errorLevel = 0, $errorFile = '', $errorLine = 0) {
		$this->message = $message;
		$this->file = $errorFile;
		$this->line = $errorLine;
	}
}