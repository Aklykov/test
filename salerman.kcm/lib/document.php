<?php
namespace Salerman\Kcm;

use Bitrix\Main,
	Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

/**
 * Class DocumentTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> DOC_ID int optional
 * <li> PARENT_DOC_ID int optional
 * <li> COMPANY_ID int optional
 * <li> NAME string optional
 * </ul>
 *
 * @package Salerman\Kcm
 **/

class DocumentTable extends Main\Entity\DataManager
{

	const ADMIN_DETAIL_URL = '/bitrix/admin/salerman_documents_edit.php?&ID=';

	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'salerman_kcm_document';
	}

	public static function getMap()
	{
		return array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true,
				'title' => Loc::getMessage('DOCUMENT_ENTITY_ID_FIELD'),
			),
			'DOC_ID' => array(
				'data_type' => 'string',
				'title' => Loc::getMessage('DOCUMENT_ENTITY_DOC_ID_FIELD'),
			),
			'PARENT_DOC_ID' => array(
				'data_type' => 'string',
				'title' => Loc::getMessage('DOCUMENT_ENTITY_PARENT_DOC_ID_FIELD'),
			),
			'COMPANY_ID' => array(
				'data_type' => 'integer',
				'title' => Loc::getMessage('DOCUMENT_ENTITY_COMPANY_ID_FIELD'),
			),
			'NAME' => array(
				'data_type' => 'string',
				'title' => Loc::getMessage('DOCUMENT_ENTITY_NAME_FIELD'),
			),
			'DATE' => array(
				'data_type' => 'datetime',
				'title' => Loc::getMessage('DOCUMENT_ENTITY_DATE_FIELD'),
			),
		);
	}

	static public function getSelectParendDocIdByCompany( $companyId=0 )
	{
		$arListParendDoc = array();
		if( $companyId > 0 ) {
			$params = array(
				'select' => array('DOC_ID', 'NAME'),
				'filter' => array(
					'COMPANY_ID' => $companyId,
					'PARENT_DOC_ID' => false,
				),
				'order' => array('DATE' => 'DESC')
			);
			$rsDocuments = self::getList($params);
			while( $arDocuments = $rsDocuments->fetch() ) {
				$arListParendDoc[$arDocuments['DOC_ID']] = $arDocuments['NAME'];
			}
		}
		return $arListParendDoc;
	}

	/**
	 * Существует ли договор с внешним ИД $docId
	 *
	 * @param string $docId
	 * @return bool
	 */
	static public function isExistDocId($docId='')
	{
		if( !empty($docId) ) {
			$params = array(
				'select' => array('DOC_ID'),
				'filter' => array('DOC_ID' => $docId),
				'limit' => 1
			);
			$rsDocuments = self::getList($params);
			if( $arDocuments = $rsDocuments->fetch() ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Получить ИД элемента по внешнему ИД
	 *
	 * @param $DocId
	 * @return int|bool
	 */
	static public function getIdByDocId($docId='')
	{
		if( !empty($docId) ) {
			$params = array(
				'select' => array('ID'),
				'filter' => array(
					'DOC_ID' => $docId,
				),
				'limit' => 1
			);
			$rsDocuments = self::getList($params);
			if( $arDocuments = $rsDocuments->fetch() ) {
				return $arDocuments['ID'];
			}
		}
		return false;
	}

	/**
	 * Получить ИД элемента по внешнему ИД и ИД компании
	 *
	 * @param $DocId
	 * @param $СompanyId
	 * @return int|bool
	 */
	static public function getIdByDocIdAndCompany($docId='', $companyId='')
	{
		if( !empty($docId) ) {
			$params = array(
				'select' => array('ID'),
				'filter' => array(
					'DOC_ID' => $docId,
					'COMPANY_ID' => $companyId,
				),
				'limit' => 1
			);
			$rsDocuments = self::getList($params);
			if( $arDocuments = $rsDocuments->fetch() ) {
				return $arDocuments['ID'];
			}
		}
		return false;
	}

	/**
	 * Получить элемент по внешнему ИД
	 *
	 * @param $DocId
	 * @return array|bool
	 */
	static public function getByDocId($docId='')
	{
		if( !empty($docId) ) {
			$params = array(
				'filter' => array(
					'DOC_ID' => $docId,
				),
				'limit' => 1
			);
			$rsDocuments = self::getList($params);
			if( $arDocuments = $rsDocuments->fetch() ) {
				return $arDocuments;
			}
		}
		return false;
	}

	/**
	 * Получить элемент по внешнему ИД и ИД-компании
	 *
	 * @param $DocId
	 * @return array|bool
	 */
	static public function getByDocIdAndCompany($docId='', $companyId='')
	{
		if( !empty($docId) && !empty($companyId) ) {
			$params = array(
				'filter' => array(
					'DOC_ID' => $docId,
					'COMPANY_ID' => $companyId,
				),
				'limit' => 1
			);
			$rsDocuments = self::getList($params);
			if( $arDocuments = $rsDocuments->fetch() ) {
				return $arDocuments;
			}
		}
		return false;
	}

	/**
	 * Получить HTML-ссылку на деталку документа
	 *
	 * @param string $docId
	 * @return string
	 */
	static public function getHtmlLinkDetailByDocId($docId='')
	{
		$linkDetail = '';
		if( !empty($docId) ) {
			$arDocument = self::getByDocId($docId);
			if( !empty($arDocument) ) {
				$urlDetail = self::ADMIN_DETAIL_URL . $arDocument['ID'];
				$linkDetail = '<a href="'.$urlDetail.'" title="title">'.$arDocument['NAME'].'</a>';
			}
		}
		return $linkDetail;
	}

	/**
	 * Получить json списка документов (с допниками)
	 *
	 * @param int $companyId
	 * @return array
	 */
	static public function getJsonDocumentsByCompanyId($companyId=0)
	{
		$arDoc = array();
		if( $companyId > 0 ) {
			$arParentDocs = array();
			$arChildrenDocs = array();
			$params = array(
				'select' => array('ID', 'NAME', 'DOC_ID', 'PARENT_DOC_ID'),
				'filter' => array(
					'COMPANY_ID' => $companyId
				),
				'order' => array('DATE' => 'DESC')
			);
			$rsElement = self::getList($params);
			while( $arElement = $rsElement->Fetch() ) {
				if( empty($arElement['PARENT_DOC_ID']) ) {
					$arParentDocs[] = array(
						'name' => $arElement['NAME'],
						'id' => $arElement['DOC_ID'],
						'selected' => false,
						'subArrDocsID' => array(),
						'subArrDocs' => array(
							array('subName' => 'Выберите доп. соглашение', 'subId' => $arElement['DOC_ID'])
						),
					);
				} else {
					$arChildrenDocs[] = $arElement;
				}
			}
			foreach( $arParentDocs as $k => $arParentDoc ) {
				foreach( $arChildrenDocs as $k2 => $arChildrenDoc ) {
					if( $arParentDoc['id'] == $arChildrenDoc['PARENT_DOC_ID'] ) {
						$arParentDocs[$k]['subArrDocs'][] = array(
							'subName' => $arChildrenDoc['NAME'],
							'subId' => $arChildrenDoc['DOC_ID'],
							'selected' => false,
						);
						$arParentDocs[$k]['subArrDocsID'][] = $arChildrenDoc['DOC_ID'];
					}
				}
			}
			$arDoc = $arParentDocs;
		}
		return $arDoc;
	}

}