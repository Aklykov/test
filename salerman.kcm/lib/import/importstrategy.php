<?php
namespace Salerman\Kcm\Import;

/**
 * Интерфейс классов-стратегий для импорта
 *
 * Class ImportStrategy
 * @package Salerman\Kcm\Import
 */
interface ImportStrategy
{

	const SUBDIR_NEW = '/NEW/';
	const SUBDIR_ARCHIVE = '/ARCHIVE/';
	const SUBDIR_ERROR = '/ERROR/';

	/**
	 * Получить путь до поддиректории NEW
	 *
	 * @return string
	 */
	function getSubdirNew();

	/**
	 * Получить путь до поддиректории ERROR
	 *
	 * @return string
	 */
	function getSubdirError();

	/**
	 * Получить путь до поддиректории ARCHIVE
	 *
	 * @return string
	 */
	function getSubdirArchive();

	/**
	 * Проверка корректности xml
	 *
	 * @param \SimpleXMLElement $objXml
	 * @return bool
	 */
	function checkXml($objXml);

	/**
	 * Получить строку json из xml
	 *
	 * @param \SimpleXMLElement $objXml
	 * @return mixed
	 */
	function getJson($objXml);

	/**
	 * "Сохранить файлы" (Перенести в нужные папки)
	 * /path/#FC_CONTRACT#/#NUM#/#FILENAME_PDF#
	 */
	function savePdf();

	/**
	 * Получить все возможные пути файлов откуда брать (NEW) и куда сохранять (ARCHIVE) PDF
	 *
	 * @param $objXml
	 * @param $FC_CONTRACT
	 * @param $NUM
	 * @return mixed
	 */
	function getListPathPdf($objXml, $FC_CONTRACT, $FC_BP, $NUM);

	/**
	 * Сохранить строку в БД
	 *
	 * @param \SimpleXMLElement $objXml
	 * @return mixed
	 */
	function save($objXml);

}