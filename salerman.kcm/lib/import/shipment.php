<?php
namespace Salerman\Kcm\Import;

use \Salerman\Kcm\ShipmentTable as ShipmentTable;
use \Salerman\Kcm\DocumentTable as DocumentTable;
use \Salerman\Kcm\Company as Company;
use \Salerman\Kcm\FileMap as FileMap;
use \Salerman\Kcm\Exception\DocumentImportException as DocumentImportException;
use \Salerman\Kcm\Exception\CompanyImportException as CompanyImportException;
use \Salerman\Kcm\Exception\ShipmentImportException as ShipmentImportException;

/**
 * Класс-стратегия реализует логику импорта "Отгрузка"
 *
 * Class Shipment
 * @package Salerman\Kcm\Import
 */
class Shipment implements ImportStrategy
{
	public $arListPathPdf;

	function getSubdirNew()
	{
		return KCM__PATH_TO_XML_BP3.self::SUBDIR_NEW;
	}

	function getSubdirError()
	{
		return KCM__PATH_TO_XML_BP3.self::SUBDIR_ERROR;
	}

	function getSubdirArchive()
	{
		return KCM__PATH_TO_XML_BP3.self::SUBDIR_ARCHIVE;
	}

	/**
	 * Получить путь до файла (откуда его брать)
	 *
	 * @param string $filename
	 * @return string
	 */
	static public function getPathFileFrom($filename='')
	{
		return KCM__PATH_TO_PDF_BP3.self::SUBDIR_NEW.$filename;
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
		return KCM__PATH_TO_PDF_BP3.self::SUBDIR_ARCHIVE.$FC_CONTRACT.'/'.$NUM.'/'.$filename;
	}

	/**
	 * Получить путь до файла (куда его перенести в случае ошибки)
	 *
	 * @param string $filename
	 * @return string
	 */
	static public function getPathFileError($filename='')
	{
		return KCM__PATH_TO_PDF_BP3.self::SUBDIR_ERROR.$filename;
	}

	function checkXml ($objXml)
	{
		$arXml = object_to_array($objXml);
		if( empty($arXml['R_SHIPPING']['FC_CONTRACT']) && empty($arXml['R_FG']['FC_CONTRACT_ADD']) ) {
			throw new ShipmentImportException('Не заполнено обязательное поле FC_CONTRACT или FC_CONTRACT_ADD (номер договора)');
		}
		if( empty($arXml['R_SHIPPING']['FC_BP']) ) {
			throw new ShipmentImportException('Не заполнено обязательное поле FC_BP (номер контрагента)');
		}
		if( empty($arXml['R_SHIPPING']['FK_DOC_NUM']) ) {
			throw new ShipmentImportException('Не заполнено обязательное поле FK_DOC_NUM (Номер поставки)');
		}
		return true;
	}

	function getJson($objXml)
	{
		$arXml = object_to_array($objXml);

		$subitem2 = array(
			'id' => $arXml['R_SHIPPING']['FK_DOC_NUM'],
			'docs' => array(), // array('name' => 'filename', 'href' => 'filehref')
			'table' => array()
		);

		// Заполняем docs
		foreach( $this->arListPathPdf as $arPdf ) {
			// Если файл есть в папке NEW(будет перемещен) ИЛИ в ARCHIVE(уже перенесен) прописываем его в json
			if( file_exists($arPdf['pathFrom']) || file_exists($arPdf['pathTo']) ) {
				$subitem2['docs'][] = array(
					'name' => $arPdf['name'],
					'href' => $arPdf['href']
				);
			}
		}

		// Заполняем table
		if( !empty($arXml['R_SHIPPING']['T_BATCH']['R_BATCH'][0]) ) {
			$arR_BATCH = $arXml['R_SHIPPING']['T_BATCH']['R_BATCH'];
		} else {
			$arR_BATCH = $arXml['R_SHIPPING']['T_BATCH'];
		}
		foreach( $arR_BATCH as $indexRBatch => $R_BATCH ) {
			$FC_MMR_NAME = $R_BATCH['FC_MMR_NAME'];
			$FC_BATCH = $R_BATCH['FC_BATCH'];
			$FN_MASS = $R_BATCH['FN_MASS'];
			if( !empty($R_BATCH['T_CONT']['R_CONT'][0]) ) {
				$arR_CONT = $R_BATCH['T_CONT']['R_CONT'];
			} else {
				$arR_CONT = $R_BATCH['T_CONT'];
			}
			$subitem2['table'][$indexRBatch] = array(
				'name' => $FC_MMR_NAME,
				'num' => $FC_BATCH, // Номер слитка
				'weight' => $FN_MASS, // Лигатурная масса
				'td' => array(),
			);
			foreach( $arR_CONT as $R_CONT ) {
				$FK_METAL = strtolower($R_CONT['FK_METAL']);
				$FN_CONT_QUAN = $R_CONT['FN_CONT_QUAN'];
				$subitem2['table'][$indexRBatch]['td'][] = array(
//					'name' => $FC_MMR_NAME,
//					'num' => $FC_BATCH, // Номер слитка
					'metal' => $FK_METAL,
//					'weight' => $FN_MASS, // Лигатурная масса
					'HChG' => $FN_CONT_QUAN, // ХЧГ
				);
			}
		}

		$jsonSubitems = \CUtil::PhpToJSObject($subitem2);
		return $jsonSubitems;
	}

	function getListPathPdf($objXml, $FC_CONTRACT, $FC_BP, $NUM)
	{
		$arListPathPdf = array();
		$arXml = object_to_array($objXml);
		// Получаем справочник файлов
		$pdf = $arXml['R_SHIPPING']['FC_DOCS'];
		$arFileMap = FileMap::getListBP3();
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
					'href' => '/file.php?entity=shipment&doc_id=' . $FC_CONTRACT . '&num_id=' . $NUM . '&fc_bp=' . $FC_BP . '&filename=' . $filename,
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

	function getListMetal($objXml)
	{
		$arListMetal = array();
		$arXml = object_to_array($objXml);
		if( !empty($arXml['R_SHIPPING']['T_BATCH']['R_BATCH'][0]) ) {
			$arR_BATCH = $arXml['R_SHIPPING']['T_BATCH']['R_BATCH'];
		} else {
			$arR_BATCH = $arXml['R_SHIPPING']['T_BATCH'];
		}
		foreach( $arR_BATCH as $indexRBatch => $R_BATCH ) {
			if( !empty($R_BATCH['T_CONT']['R_CONT'][0]) ) {
				$arR_CONT = $R_BATCH['T_CONT']['R_CONT'];
			} else {
				$arR_CONT = $R_BATCH['T_CONT'];
			}
			foreach( $arR_CONT as $R_CONT ) {
				$R_CONT['FK_METAL'] = strtolower($R_CONT['FK_METAL']);
				$arListMetal[$R_CONT['FK_METAL']] += floatVal($R_CONT['FN_CONT_QUAN']);
			}
		}
		return $arListMetal;
	}

	function save ($objXml)
	{
		$arXml = object_to_array($objXml);
		$FC_BP = $arXml['R_SHIPPING']['FC_BP'];
		$FC_CONTRACT = $arXml['R_SHIPPING']['FC_CONTRACT'];
		$FC_CONTRACT_ADD = $arXml['R_SHIPPING']['FC_CONTRACT_ADD'];
		$FK_DOC_NUM = $arXml['R_SHIPPING']['FK_DOC_NUM'];
		$FD_DOC_DATE = $arXml['R_SHIPPING']['FD_DOC_DATE'];
		$IS_STORNO = $arXml['R_SHIPPING']['FB_IS_STORNO'] == 'false' ? 0 : 1;

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
			throw new CompanyImportException('Контрагент '.$FC_BP.' не найден в системе');
		}

		// Получить все возможные пути файлов откуда брать (NEW) и куда сохранять (ARCHIVE) PDF
		$this->arListPathPdf = $this->getListPathPdf($objXml, $FC_CONTRACT, $FC_BP, $FK_DOC_NUM);

		// Получаем Json полей
		$json = $this->getJson($objXml);

		// Получаем список металов
		$arListMetal = self::getListMetal($objXml);

		$data = array(
			'DOC_ID' => $FC_CONTRACT,
			'FC_BP' => $FC_BP,
			'JSON' => $json,
			'FK_DOC_NUM' => $FK_DOC_NUM,
			'FD_DOC_DATE' => \Bitrix\Main\Type\Date::createFromTimestamp(strtotime($FD_DOC_DATE)),
			'FC_METAL_PT' => $arListMetal['pt'],
			'FC_METAL_PD' => $arListMetal['pd'],
			'FC_METAL_RH' => $arListMetal['rh'],
			'FC_METAL_IR' => $arListMetal['ir'],
			'FC_METAL_RU' => $arListMetal['ru'],
			'FC_METAL_OS' => $arListMetal['os'],
			'FC_METAL_AU' => $arListMetal['au'],
			'FC_METAL_AG' => $arListMetal['ag'],
			'FC_METAL_RE' => $arListMetal['re'],
			'IS_STORNO' => $IS_STORNO,
		);

		if( $arElement = ShipmentTable::getByInfo($FC_CONTRACT, $FC_BP, $FK_DOC_NUM) ) {
			$objResult = ShipmentTable::update($arElement['ID'], $data);
			if( $objResult->isSuccess() === false ) {
				$arErrors = $objResult->getErrorMessages();
				throw new ShipmentImportException($arErrors[0]);
			}
		} else {
			$objResult = ShipmentTable::add($data);
			if( $objResult->isSuccess() === false ) {
				$arErrors = $objResult->getErrorMessages();
				throw new ShipmentImportException($arErrors[0]);
			}
		}

		// Сохраняем(Перемещаем) файлы
		$this->savePdf();

		// Отмечаем флаг что были изменения в БД
		$GLOBALS['IS_CHANGE'] = true;
	}
}