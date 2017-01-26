<?php
namespace Salerman\Kcm;

use Bitrix\Main,
	Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

/**
 * Class ReportTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> DOC_ID string(30) optional
 * <li> FC_BP string(30) optional
 * <li> FK_DOC_NUM string(30) optional
 * <li> FD_DOC_DATE datetime optional
 * <li> FILE_NAME string optional
 * <li> FILE_HREF string optional
 * </ul>
 *
 * @package Salerman\Kcm
 **/

class ReportTable extends Main\Entity\DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'salerman_kcm_report';
	}

	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap()
	{
		return array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true,
				'title' => Loc::getMessage('REPORT_ENTITY_ID_FIELD'),
			),
			'DOC_ID' => array(
				'data_type' => 'string',
				'validation' => array(__CLASS__, 'validateDocId'),
				'title' => Loc::getMessage('REPORT_ENTITY_DOC_ID_FIELD'),
			),
			'FC_BP' => array(
				'data_type' => 'string',
				'validation' => array(__CLASS__, 'validateFcBp'),
				'title' => Loc::getMessage('REPORT_ENTITY_FC_BP_FIELD'),
			),
			'FK_DOC_NUM' => array(
				'data_type' => 'string',
				'validation' => array(__CLASS__, 'validateFkDocNum'),
				'title' => Loc::getMessage('REPORT_ENTITY_FK_DOC_NUM_FIELD'),
			),
			'FD_DOC_DATE' => array(
				'data_type' => 'datetime',
				'title' => Loc::getMessage('REPORT_ENTITY_FD_DOC_DATE_FIELD'),
			),
			'FILE_NAME' => array(
				'data_type' => 'text',
				'title' => Loc::getMessage('REPORT_ENTITY_FILE_NAME_FIELD'),
			),
			'FILE_HREF' => array(
				'data_type' => 'text',
				'title' => Loc::getMessage('REPORT_ENTITY_FILE_HREF_FIELD'),
			),
		);
	}
	/**
	 * Returns validators for DOC_ID field.
	 *
	 * @return array
	 */
	public static function validateDocId()
	{
		return array(
			new Main\Entity\Validator\Length(null, 30),
		);
	}
	/**
	 * Returns validators for FC_BP field.
	 *
	 * @return array
	 */
	public static function validateFcBp()
	{
		return array(
			new Main\Entity\Validator\Length(null, 30),
		);
	}
	/**
	 * Returns validators for FK_DOC_NUM field.
	 *
	 * @return array
	 */
	public static function validateFkDocNum()
	{
		return array(
			new Main\Entity\Validator\Length(null, 30),
		);
	}

	/**
	 * Получить элемент сущности
	 *
	 * @param $DOC_ID - номер договора
	 * @param $FC_BP - номер контрагента
	 * @param $DOC_NUM - номер поставки
	 * @return array|bool
	 */
	public static function getByInfo($DOC_ID, $FC_BP, $DOC_NUM)
	{
		$params = array(
			'filter' => array(
				'DOC_ID' => $DOC_ID,
				'FC_BP' => $FC_BP,
				'FK_DOC_NUM' => $DOC_NUM,
			),
			'limit' => 1,
		);
		$rsElement = self::getList($params);
		if( $arElement = $rsElement->Fetch() ){
			return $arElement;
		}
		return false;
	}

}