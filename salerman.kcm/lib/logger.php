<?php
namespace Salerman\Kcm;

/**
 * Класс для ведения лога
 *
 * Class Logger
 * @package Salerman\Kcm
 */
class Logger
{
	const LOG_PATH = '/upload/import.log'; // Путь к файлу-логу
	const TEMPLATE_EVENT = 'SALERMAN_KCM'; // Тип почтового события
	const TEMPLATE_MESSAGE_ID = 29; // ИД почтового шаблона

	private $arMessages = array();
	private static $instance = null;

	private function __construct()
	{

	}

	/**
	 * Получить сущность Logger
	 *
	 * @return Logger
	 */
	static public function getInstance()
	{
		if(is_null(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Получить массив сообщений одной строкой
	 *
	 * @return string
	 */
	private function getMessageString()
	{
		return implode('', $this->arMessages);
	}

	/**
	 * Очистить массив сообщений
	 */
	public function clear()
	{
		$this->arMessages = array();
	}

	/**
	 * Добавить сообщение
	 *
	 * @param string $message
	 */
	public function add($message)
	{
		$this->arMessages[] = $message;
	}

	/**
	 * Записать все сообщения в лог-файл
	 */
	private function write()
	{
		if( !LOGGER__IS_WRITE ) return;
		$path = $_SERVER['DOCUMENT_ROOT'].self::LOG_PATH;
		$text = $this->getMessageString();
		file_put_contents($path, $text, FILE_APPEND);
	}

	/**
	 * Разослать все сообщения на почту
	 */
	private function send()
	{
		if( !LOGGER__IS_SEND ) return;
		$arEmails = User::getArrayEmailsAlertErrorImport();
		$text = $this->getMessageString();
		foreach( $arEmails as $email ) {
		    if(strlen($text)){
			$arEventFields = array(
				'ERROR_INFO' => $text,
				'EMAIL' => $email,
			);
			\CEvent::Send( self::TEMPLATE_EVENT, 's1', $arEventFields, 'N', self::TEMPLATE_MESSAGE_ID );
            }
		}
	}

	/**
	 * Сохранить накопленые сообщения
	 */
	public function save()
	{
		$this->write();
		$this->send();
		$this->clear();
	}

}
