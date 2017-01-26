<?php
namespace Salerman\Kcm\Import;

use \Salerman\Kcm\SupplyTable as SupplyTable;
use \Salerman\Kcm\DocumentTable as DocumentTable;
use \Salerman\Kcm\Company as Company;
use \Salerman\Kcm\FileMap as FileMap;
use \Salerman\Kcm\Exception\DocumentImportException as DocumentImportException;
use \Salerman\Kcm\Exception\CompanyImportException as CompanyImportException;
use \Salerman\Kcm\Exception\SupplyImportException as SupplyImportException;
use Salerman\Kcm\Logger;

/**
 * Класс-стратегия реализует логику импорта "Поставка"
 *
 * Class Supply
 * @package Salerman\Kcm\Import
 */
class Supply implements ImportStrategy
{
	public $arListPathPdf;

	function getSubdirNew()
	{
		return KCM__PATH_TO_XML_BP1.self::SUBDIR_NEW;
	}

	function getSubdirError()
	{
		return KCM__PATH_TO_XML_BP1.self::SUBDIR_ERROR;
	}

	function getSubdirArchive()
	{
		return KCM__PATH_TO_XML_BP1.self::SUBDIR_ARCHIVE;
	}

	/**
	 * Получить путь до файла (откуда его брать)
	 *
	 * @param string $filename
	 * @return string
	 */
	static public function getPathFileFrom($filename='')
	{
		return KCM__PATH_TO_PDF_BP1.self::SUBDIR_NEW.$filename;
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
		return KCM__PATH_TO_PDF_BP1.self::SUBDIR_ARCHIVE.$FC_CONTRACT.'/'.$NUM.'/'.$filename;
	}

	/**
	 * Получить путь до файла (куда его перенести в случае ошибки)
	 *
	 * @param string $filename
	 * @return string
	 */
	static public function getPathFileError($filename='')
	{
		return KCM__PATH_TO_PDF_BP1.self::SUBDIR_ERROR.$filename;
	}

	/**
	 * Получить наименование Поставки
	 *
	 * @param string $FC_DOC_SUP Номер документа
	 * @param string $FD_DOC_SUP Дата документа
	 * @param string $FC_DOC_SUP_TYPE Тип документа
	 * @return string
	 */
	static public function getSupplyName($FC_DOC_SUP, $FD_DOC_SUP, $FC_DOC_SUP_TYPE)
	{
		$supplyName = '';
		$supplyName .= !empty($FC_DOC_SUP_TYPE) ? $FC_DOC_SUP_TYPE.' ': '';
		$supplyName .= !empty($FC_DOC_SUP) ? '№'.$FC_DOC_SUP.' ': '';
		$supplyName .= !empty($FD_DOC_SUP) ? 'от '.date('d.m.Y', strtotime($FD_DOC_SUP)): '';
		$supplyName = strtoupper(substr($supplyName, 0, 1)) . strtolower(substr($supplyName, 1, strlen($supplyName)));
		return $supplyName;
	}

	function checkXml($objXml)
	{
		$arXml = object_to_array($objXml);
		if( empty($arXml['R_SUPPLY']['FC_CONTRACT']) && empty($arXml['R_SUPPLY']['FC_CONTRACT_ADD']) ) {
			throw new SupplyImportException('Не заполнено обязательное поле FC_CONTRACT или FC_CONTRACT_ADD (номер договора)');
		}
		if( empty($arXml['R_SUPPLY']['FC_BP']) ) {
			throw new SupplyImportException('Не заполнено обязательное поле FC_BP (номер контрагента)');
		}
		if( empty($arXml['R_SUPPLY']['FK_NUM']) ) {
			throw new SupplyImportException('Не заполнено обязательное поле FK_NUM (Номер поставки)');
		}
		return true;
	}

	function getJson($objXml)
	{
		$arXml = object_to_array($objXml);
		$arSubitems = array();
		$index = 0;
		if( !empty($arXml['R_SUPPLY']['T_BATCH']['R_BATCH'][0]) ) {
			$arR_BATCH = $arXml['R_SUPPLY']['T_BATCH']['R_BATCH'];
		} else {
			$arR_BATCH = $arXml['R_SUPPLY']['T_BATCH'];
		}
		foreach( $arR_BATCH as $k => $arItem ) {
			// Генерим уникальный id для элементов второй колонки
			$id = md5(serialize($arItem)) . '_' . $index;
			// Вторая колонка
			$arSubitems__item = array(
				'id' => $id,
				'num' => $arItem['FK_BATCH_NUM'],
				'date' => ($arItem['FD_BATCH_DATE']) ? $arItem['FD_BATCH_DATE']: '',
				'name' => ($arItem['FC_MMR_NAME']) ? $arItem['FC_MMR_NAME']: '',
				'weight1' => ($arItem['FN_LM_SUP']) ? $arItem['FN_LM_SUP']: '',
				'weight2' => ($arItem['FN_LM']) ? $arItem['FN_LM']: '',
				'subitem2' => array(
					'id' => '',
					'doks' => array(),
					'tablecontent' => array(),
				),
			);
			// Третья колонка (doks)
			foreach( $this->arListPathPdf[$arItem['FK_BATCH_NUM']] as $arPdf ) {
				// Если файл есть в папке NEW(будет перемещен) ИЛИ в ARCHIVE(уже перенесен) прописываем его в json
				if( file_exists($arPdf['pathFrom']) || file_exists($arPdf['pathTo']) ) {
					$arSubitems__item['subitem2']['doks'][] = array(
						'name' => $arPdf['name'],
						'href' => $arPdf['href']
					);
				}
			}
			// Третья колонка (tablecontent)
			if( !empty($arItem['T_CONT']['R_CONT'][0]) ) {
				$arR_CONT = $arItem['T_CONT']['R_CONT'];
			} else {
				$arR_CONT = $arItem['T_CONT'];
			}
			foreach( $arR_CONT as $arItemSub ) {
				$arItemSub['FK_METAL'] = strtolower($arItemSub['FK_METAL']);
				$arItemSub['FN_CONT_PROC'] = round(floatVal($arItemSub['FN_CONT_PROC']), 2);
				$arSubitems__item['subitem2']['tablecontent'][] = array(
					'name' => $arItemSub['FK_METAL'],
					'value' => $arItemSub['FN_CONT_PROC']
				);
			}
			$index += 1;
			$arSubitems[] = $arSubitems__item;
		}
		$jsonSubitems = \CUtil::PhpToJSObject($arSubitems);
		return $jsonSubitems;
	}

	function getListPathPdf($objXml, $FC_CONTRACT, $FC_BP, $NUM)
	{
		$arListPathPdf = array();
		$arXml = object_to_array($objXml);
		// Получаем справочник файлов
		$arFileMap = FileMap::getListBP1();
		if( !empty($arXml['R_SUPPLY']['T_BATCH']['R_BATCH'][0]) ) {
			$arR_BATCH = $arXml['R_SUPPLY']['T_BATCH']['R_BATCH'];
		} else {
			$arR_BATCH = $arXml['R_SUPPLY']['T_BATCH'];
		}
		foreach( $arR_BATCH as $k => $R_BATCH ) {
			$pdf = $R_BATCH['FC_DOCS'];
			foreach( $arFileMap as $objFM ) {
				$arType = array('.pdf', '.PDF', '.xls', '.XLS'); // Допилил в цикле для XLS, и в будущем другие типы использовать
				foreach($arType as $type) {
					// Подставляем суффикс и ищем такой файл
					$filename = $pdf . '_' . $objFM->suffix . $type;

					$pathFrom = self::getPathFileFrom($filename);
					$pathTo = self::getPathFileTo($FC_CONTRACT, $NUM, $filename);
					$pathError = self::getPathFileError($filename);
					$arListPathPdf[$R_BATCH['FK_BATCH_NUM']][] = array(
						'name' => $objFM->name,
						'href' => '/file.php?entity=supply&doc_id=' . $FC_CONTRACT . '&num_id=' . $NUM . '&fc_bp=' . $FC_BP . '&filename=' . $filename,
						'pathFrom' => $pathFrom,
						'pathTo' => $pathTo,
						'pathError' => $pathError
					);
				}
			}
		}
		return $arListPathPdf;
	}

	function savePdf()
	{
		foreach( $this->arListPathPdf as $arItem ) {
			foreach( $arItem as $arPdf ) {
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
	}

	/**
	 * Получить список металов (сумму весов) по каждому контрагенту
	 *
	 * @param $objXml
	 * @return array
	 */
	function getListMetalsBP($objXml)
	{
		$arListMetalsBP = array();
		$arXml = object_to_array($objXml);
		if( !empty($arXml['R_SUPPLY']['T_BATCH']['R_BATCH'][0]) ) {
			$arR_BATCH = $arXml['R_SUPPLY']['T_BATCH']['R_BATCH'];
		} else {
			$arR_BATCH = $arXml['R_SUPPLY']['T_BATCH'];
		}
		foreach( $arR_BATCH as $k => $R_BATCH ) {
			if( !empty($R_BATCH['T_CONT']['R_CONT'][0]) ) {
				$arR_CONT = $R_BATCH['T_CONT']['R_CONT'];
			} else {
				$arR_CONT = $R_BATCH['T_CONT'];
			}
			foreach( $arR_CONT as $R_CONT ) {
				$R_CONT['FC_OWNER'] = $R_CONT['FC_OWNER'];
				$R_CONT['FK_METAL'] = strtolower($R_CONT['FK_METAL']);
				if( isset($arListMetalsBP[$R_CONT['FC_OWNER']][$R_CONT['FK_METAL']]) == false ) {
					$arListMetalsBP[$R_CONT['FC_OWNER']][$R_CONT['FK_METAL']] = 0;
				}
				$arListMetalsBP[$R_CONT['FC_OWNER']][$R_CONT['FK_METAL']] += $R_CONT['FN_CONT_QUANT'];
			}
		}
		// Если нет металов - это значит что поставка неуточненная. Делаем заглушку вместо FC_OWNER берем FC_BP
		if( empty($arListMetalsBP) ) {
			$arListMetalsBP[$arXml['R_SUPPLY']['FC_BP']] = array();
		}

		return $arListMetalsBP;
	}

	function save ($objXml)
	{
		$arXml = object_to_array($objXml);
		$FC_BP = $arXml['R_SUPPLY']['FC_BP'];
		$FC_CONTRACT = $arXml['R_SUPPLY']['FC_CONTRACT'];
		$FC_CONTRACT_ADD = $arXml['R_SUPPLY']['FC_CONTRACT_ADD'];
		$FK_NUM = $arXml['R_SUPPLY']['FK_NUM'];
		$FD_DATE = $arXml['R_SUPPLY']['FD_DATE'];
		$FC_BOX = $arXml['R_SUPPLY']['FC_BOX'];
		$FN_MASS_SUP = $arXml['R_SUPPLY']['FN_MASS_SUP'];
		$FC_DOC_SUP = $arXml['R_SUPPLY']['FC_DOC_SUP'];
		$FD_DOC_SUP = $arXml['R_SUPPLY']['FD_DOC_SUP'];
		$FC_DOC_SUP_TYPE = $arXml['R_SUPPLY']['FC_DOC_SUP_TYPE'];
		$FN_QUANT = $arXml['R_SUPPLY']['FN_QUANT'];
		$FN_QUANT = $arXml['R_SUPPLY']['FN_QUANT'];
		$IS_STORNO = $arXml['R_SUPPLY']['FB_IS_STORNO'] == 'false' ? 0 : 1;

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

		// Формируем имя документа
		$SUPPLY_NAME = Supply::getSupplyName($FC_DOC_SUP, $FD_DOC_SUP, $FC_DOC_SUP_TYPE);

		// Получить все возможные пути файлов откуда брать (NEW) и куда сохранять (ARCHIVE) PDF
		$this->arListPathPdf = $this->getListPathPdf($objXml, $FC_CONTRACT, $FC_BP, $FK_NUM);

		// Получаем Json полей
		$json = $this->getJson($objXml);

		// Получаем список металлов для каждого контрагента
		$arListMetalsBP = $this->getListMetalsBP($objXml);

		// Сохраняем каждого контрагента в отдельной строке
		foreach( $arListMetalsBP as $FC_BP => $arMetals ) {
			// Проверяем существование компании (FC_OWNER)
			if( Company::isExistFcBp($FC_BP) === false ) {
				$message = date('d.m.Y H:i:s ') . ' Файл: ' . Context::$currFile . "\n"; // Где и Когда случилось
				$message .= 'Ошибка: Сложная поставка. Контрагент не найден по FC_OWNER: ' . $FC_BP . "\n"; // Что случилось
				Logger::getInstance()->add($message);
				continue;
			}
			$data = array(
				'DOC_ID' => $FC_CONTRACT,
				'FC_BP' => $FC_BP,
				'JSON' => $json,
				'FK_NUM' => $FK_NUM,
				'FD_DATE' => \Bitrix\Main\Type\Date::createFromTimestamp(strtotime($FD_DATE)),
				'FC_BOX' => $FC_BOX,
				'FN_MASS_SUP' => $FN_MASS_SUP,
				'FC_DOC_SUP' => $SUPPLY_NAME, // Имя поставки (формируется из 3 полей)
				'FN_QUANT' => $FN_QUANT,
				'FC_METAL_PT' => $arMetals['pt'],
				'FC_METAL_PD' => $arMetals['pd'],
				'FC_METAL_RH' => $arMetals['rh'],
				'FC_METAL_IR' => $arMetals['ir'],
				'FC_METAL_RU' => $arMetals['ru'],
				'FC_METAL_OS' => $arMetals['os'],
				'FC_METAL_AU' => $arMetals['au'],
				'FC_METAL_AG' => $arMetals['ag'],
				'FC_METAL_RE' => $arMetals['re'],
				'IS_STORNO' => $IS_STORNO,
			);

			if( $arElement = SupplyTable::getByInfo($FC_CONTRACT, $FC_BP, $FK_NUM) ) {
				$objResult = SupplyTable::update($arElement['ID'], $data);
				if( $objResult->isSuccess() === false ) {
					$arErrors = $objResult->getErrorMessages();
					throw new SupplyImportException($arErrors[0]);
				}
			} else {
				$objResult = SupplyTable::add($data);
				if( $objResult->isSuccess() === false ) {
					$arErrors = $objResult->getErrorMessages();
					throw new SupplyImportException($arErrors[0]);
				}
			}
		}

		// Сохранение файлов
		$this->savePdf();

		// Отмечаем флаг что были изменения в БД
		$GLOBALS['IS_CHANGE'] = true;
	}
}