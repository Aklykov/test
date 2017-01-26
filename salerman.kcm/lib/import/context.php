<?php
namespace Salerman\Kcm\Import;
use Salerman\Kcm\Exception\ImportException as ImportException;
use Salerman\Kcm\Exception\XmlImportException as XmlImportException;
use Salerman\Kcm\Logger;




/**
 * Класс контекст выполнения логики импорта
 *
 * Class Context
 * @package Salerman\Kcm\Import
 */
class Context
{
	/**
	 * @var ImportStrategy
	 */
	private $importStrategy;
	private $objXml;
	private $subdirNew; // Путь поддиректории NEW
	private $subdirError; // Путь поддиректории ERROR
	private $subdirArchive; // Путь поддиректории ARCHIVE

	static public $currFile; // Путь текущего файла (импорт которого происходит)

	/**
	 * @param ImportStrategy $strategy
	 */
	function __construct(ImportStrategy $strategy)
	{
		$this->importStrategy = $strategy;
	}

	function getNow(){
	    return date("Ymd-his").".xml";
    }


	/**
	 * Чтение файлов из каталога по дате
	 *
	 * @param string $path
	 * @return array
	 */
	function getFilesFromDirectory($path)
	{
		$arFiles = array();
		foreach(scandir($path) as $entry) {
			if( strlen($entry) > 5 ) {
				$filectime = filemtime($path.$entry);
				$arFiles[$filectime] = $path.$entry;
			}
		}
		ksort($arFiles);
		return $arFiles;
	}

	function readFile($file)
	{
		$strXml = file_get_contents($file);
		$objXml = simplexml_load_string($strXml);



		if( get_class($objXml) != 'SimpleXMLElement' ) {
			throw new XmlImportException('Файл не соответствует формату xml');
		}

		$this->importStrategy->checkXml($objXml);

		return $objXml;
	}

	/**
	 * Переместить файл в архив
	 *
	 * @param $file
	 */
	function moveXmlToArchive($file)
	{
		$fileNew = $this->subdirArchive . str_replace($this->subdirNew, '', $file)."-".self::getNow();                  // дописываем файлу время обработки
		rename($file, $fileNew);
	}

	/**
	 * Переместить файл в ошибку
	 *
	 * @param $file
	 */
	function moveXmlToError($file)
	{
		$fileNew = $this->subdirError . str_replace($this->subdirNew, '', $file)."-".self::getNow();                    // дописываем файлу время обработки
		rename($file, $fileNew);
	}

	function execute()
	{
		// Получаем пути до файлов
		$this->subdirNew = $this->importStrategy->getSubdirNew();
		$this->subdirError = $this->importStrategy->getSubdirError();
		$this->subdirArchive = $this->importStrategy->getSubdirArchive();

		$arFiles = self::getFilesFromDirectory( $this->subdirNew );


		foreach( $arFiles as $file ) {
			self::$currFile = $file;
			try {
				$objXml = $this->readFile($file); // Читаем файл

				$this->importStrategy->save($objXml); // Сохраняем в БД
				$this->moveXmlToArchive($file); // Переносим в архив
			} catch (ImportException $e) {
				$message = date('d.m.Y H:i:s ') . ' Файл: ' . self::$currFile . "\n"; // Где и Когда случилось
				$message .= 'Ошибка: ' . $e->getMessage() . "\n"; // Что случилось
				Logger::getInstance()->add($message);
				$this->moveXmlToError($file); // Переносим в ошибку
			}
		}
	}
}