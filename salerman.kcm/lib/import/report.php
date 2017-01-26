<?php
namespace Salerman\Kcm\Import;

use \Salerman\Kcm\ReportTable as ReportTable;
use \Salerman\Kcm\DocumentTable as DocumentTable;
use \Salerman\Kcm\Company as Company;
use \Salerman\Kcm\FileMap as FileMap;
use \Salerman\Kcm\Exception\DocumentImportException as DocumentImportException;
use \Salerman\Kcm\Exception\CompanyImportException as CompanyImportException;
use \Salerman\Kcm\Exception\ReportImportException as ReportImportException;

/**
 * Класс-стратегия реализует логику импорта "Отчеты агентов"
 *
 * Class Report
 * @package Salerman\Kcm\Import
 */
class Report implements ImportStrategy
{
	public $arListPathPdf;

	function getSubdirNew()
	{
		return KCM__PATH_TO_XML_BP4.self::SUBDIR_NEW;
	}

	function getSubdirError()
	{
		return KCM__PATH_TO_XML_BP4.self::SUBDIR_ERROR;
	}

	function getSubdirArchive()
	{
		return KCM__PATH_TO_XML_BP4.self::SUBDIR_ARCHIVE;
	}

	/**
	 * Получить путь до файла (откуда его брать)
	 *
	 * @param string $filename
	 * @return string
	 */
	static public function getPathFileFrom($filename='')
	{
		return KCM__PATH_TO_PDF_BP4.self::SUBDIR_NEW.$filename;
	}

	/**
	 * Получить путь до файла (куда его перенести в случае успеха)
	 *
	 * @param string $FC_CONTRACT
	 * @param string $NUM
	 * @param string $filename
	 * @return string
	 */
	static public function getPathFileTo($FC_CONTRACT='', $NUM='', $filename='')
	{
		return KCM__PATH_TO_PDF_BP4.self::SUBDIR_ARCHIVE.$FC_CONTRACT.'/'.$NUM.'/'.$filename;
	}

	/**
	 * Получить путь до файла (куда его перенести в случае ошибки)
	 *
	 * @param string $filename
	 * @return string
	 */
	static public function getPathFileError($filename='')
	{
		return KCM__PATH_TO_PDF_BP4.self::SUBDIR_ERROR.$filename;
	}

	function checkXml($objXml)
	{
		$arXml = object_to_array($objXml);

		if( empty($arXml['R_AGENT_REP']['FC_CONTRACT']) && empty($arXml['R_AGENT_REP']['FC_CONTRACT_ADD']) ) {
			throw new ReportImportException('Не заполнено обязательное поле FC_CONTRACT или FC_CONTRACT_ADD (номер договора)');
		}

		if( empty($arXml['R_AGENT_REP']['FC_BP']) ) {
			throw new ReportImportException('Не заполнено обязательное поле FC_BP (номер контрагента)');
		}

        if( empty($arXml['R_AGENT_REP']['FK_DOC_NUM'])) {
            throw new ReportImportException('Не заполнено обязательное поле FK_DOC_NUM (номер документа)');
        }

        if( empty($arXml['R_AGENT_REP']['FC_DOCS'])) {
            throw new ReportImportException('Не заполнено обязательное поле FC_DOCS (наименование документа на FTP)');
        }


		return true;
	}

	function getJson($objXml)
	{
		return '';
	}

	// Получаем список путей PDF и другие типы файла.
	function getListPathPdf($objXml, $FC_CONTRACT, $FC_BP, $NUM)
	{
		$arListPathPdf = array();
		$arXml = object_to_array($objXml);
		$pdf = $arXml['R_AGENT_REP']['FC_DOCS'];

		// Получаем справочник файлов
		$arFileMap = FileMap::getListBP4();
		foreach( $arFileMap as $objFM ) {
            $arType = array('.pdf', '.PDF', '.xls', '.XLS'); // Допилил в цикле для XLS, и в будущем другие типы использовать
            foreach($arType as $type) {
                // Подставляем суффикс и ищем такой файл
                $filename = $pdf . '_' . $objFM->suffix . $type;

                $pathFrom = self::getPathFileFrom($filename);
                $pathTo = self::getPathFileTo($FC_CONTRACT, $NUM, $filename);
                $pathError = self::getPathFileError($filename);
                $arListPathPdf[] = array(
                    'name' => $objFM->name,
                    'href' => '/file.php?entity=report&doc_id=' . $FC_CONTRACT . '&num_id=' . $NUM . '&fc_bp=' . $FC_BP . '&filename=' . $filename,
                    'pathFrom' => $pathFrom,
                    'pathTo' => $pathTo,
                    'pathError' => $pathError
                );
            }
		}
		return $arListPathPdf;
	}

	function savePdf ()
	{
		foreach( $this->arListPathPdf as $arPdf ) {
			if( file_exists($arPdf['pathFrom']) ) {
				if( CopyDirFiles( $arPdf['pathFrom'], $arPdf['pathTo'], true, true ) ) {
					// PDF скопирован успешно (Удаляем из папки NEW)
					unlink($arPdf['pathFrom']);
				} else {
					// PDF не скопирован (Перемещаем пдф в папку ERROR)
					rename($arPdf['pathFrom'], $arPdf['pathError']);
				}
			}
		}
	}

	function save ($objXml)
	{
		$arXml = object_to_array($objXml);
		$FC_BP = $arXml['R_AGENT_REP']['FC_BP'];
		$FC_CONTRACT = $arXml['R_AGENT_REP']['FC_CONTRACT'];
		$FC_CONTRACT_ADD = $arXml['R_AGENT_REP']['FC_CONTRACT_ADD'];
		$FK_DOC_NUM = $arXml['R_AGENT_REP']['FK_DOC_NUM'];
		$FD_DOC_DATE = $arXml['R_AGENT_REP']['FD_DOC_DATE'];
		// Если пришел допник - то ищем документ по нему
		if( !empty($FC_CONTRACT_ADD) ) {
			$FC_CONTRACT = $FC_CONTRACT_ADD;
		}

		// Проверяем существование документа
		if( DocumentTable::isExistDocId($FC_CONTRACT) === false ) {
			throw new DocumentImportException('Договор '.$FC_CONTRACT.' не найден в системе');
		}

		// Проверяем существование компании
		if( Company::isExistFcBp($FC_BP) === false ) {
			throw new CompanyImportException('Контрагент '.$arXml['R_AGENT_REP']['FC_BP'].' не найден в системе');
		}

		// Получить все возможные пути файлов откуда брать (NEW) и куда сохранять (ARCHIVE) PDF
		$this->arListPathPdf = $this->getListPathPdf($objXml, $FC_CONTRACT, $FC_BP, $FK_DOC_NUM);

		// Определяем $FILE_NAME и $FILE_HREF
		foreach( $this->arListPathPdf as $arPdf ) {

		    // Если файл есть в папке NEW(будет перемещен) ИЛИ в ARCHIVE(уже перенесен) прописываем его в json

			if( file_exists($arPdf['pathFrom']) || file_exists($arPdf['pathTo']) ) {
				$FILE_NAME = $arPdf['name'];
				$FILE_HREF = $arPdf['href'];
				break;
			}
		}

		// Проверка на файл, вдруг файла нет, тогда ничего не добавляем
        if(empty($FILE_HREF)){
	        throw new ReportImportException("XML данные есть, но самого файла на сервере нет (номер документа - ".$FK_DOC_NUM.")");
        }

		$data = array(
			'DOC_ID' => $FC_CONTRACT,
			'FC_BP' => $FC_BP,
			'FK_DOC_NUM' => $FK_DOC_NUM,
			'FD_DOC_DATE' => \Bitrix\Main\Type\Date::createFromTimestamp(strtotime($FD_DOC_DATE)),
			'FILE_NAME' => $FILE_NAME,
			'FILE_HREF' => $FILE_HREF,
		);

		// Сохраняем/Обновляем элемент

		if( $arElement = ReportTable::getByInfo($FC_CONTRACT, $FC_BP, $FK_DOC_NUM) ) {
			$objResult = ReportTable::update($arElement['ID'], $data);
			if( $objResult->isSuccess() === false ) {
				$arErrors = $objResult->getErrorMessages();
				throw new ReportImportException($arErrors[0]);
			}
		} else {
			$objResult = ReportTable::add($data);
			if( $objResult->isSuccess() === false ) {
				$arErrors = $objResult->getErrorMessages();
				throw new ReportImportException($arErrors[0]);
			}
		}

		// Сохраняем(Перемещаем) файлы
		$this->savePdf();

		// Отмечаем флаг что были изменения в БД
		$GLOBALS['IS_CHANGE'] = true;

	}
}