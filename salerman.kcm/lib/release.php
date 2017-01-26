<?php
namespace Salerman\Kcm;

use Bitrix\Main,
	Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

/**
 * Class ReleaseTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> DOC_ID string(30) optional
 * <li> FC_BP string(30) optional
 * <li> JSON string optional
 * <li> FK_DOC_NUM string(30) optional
 * <li> FD_DOC_DATE datetime optional
 * <li> FC_METAL_PT string(10) optional
 * <li> FC_METAL_PD string(10) optional
 * <li> FC_METAL_RH string(10) optional
 * <li> FC_METAL_IR string(10) optional
 * <li> FC_METAL_RU string(10) optional
 * <li> FC_METAL_OS string(10) optional
 * <li> FC_METAL_AU string(10) optional
 * <li> FC_METAL_AG string(10) optional
 * <li> FC_METAL_RE string(10) optional
 * <li> IS_STORNO int optional
 * </ul>
 *
 * @package Salerman\Kcm
 **/

class ReleaseTable extends Main\Entity\DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'salerman_kcm_release';
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
				'title' => Loc::getMessage('RELEASE_ENTITY_ID_FIELD'),
			),
			'DOC_ID' => array(
				'data_type' => 'string',
				'validation' => array(__CLASS__, 'validateDocId'),
				'title' => Loc::getMessage('RELEASE_ENTITY_DOC_ID_FIELD'),
			),
			'FC_BP' => array(
				'data_type' => 'string',
				'validation' => array(__CLASS__, 'validateFcBp'),
				'title' => Loc::getMessage('RELEASE_ENTITY_FC_BP_FIELD'),
			),
			'JSON' => array(
				'data_type' => 'text',
				'title' => Loc::getMessage('RELEASE_ENTITY_JSON_FIELD'),
			),
			'FK_DOC_NUM' => array(
				'data_type' => 'string',
				'validation' => array(__CLASS__, 'validateFkDocNum'),
				'title' => Loc::getMessage('RELEASE_ENTITY_FK_DOC_NUM_FIELD'),
			),
			'FD_DOC_DATE' => array(
				'data_type' => 'datetime',
				'title' => Loc::getMessage('RELEASE_ENTITY_FD_DOC_DATE_FIELD'),
			),
			'FC_METAL_PT' => array(
				'data_type' => 'string',
				'validation' => array(__CLASS__, 'validateFcMetalPt'),
				'title' => Loc::getMessage('RELEASE_ENTITY_FC_METAL_PT_FIELD'),
			),
			'FC_METAL_PD' => array(
				'data_type' => 'string',
				'validation' => array(__CLASS__, 'validateFcMetalPd'),
				'title' => Loc::getMessage('RELEASE_ENTITY_FC_METAL_PD_FIELD'),
			),
			'FC_METAL_RH' => array(
				'data_type' => 'string',
				'validation' => array(__CLASS__, 'validateFcMetalRh'),
				'title' => Loc::getMessage('RELEASE_ENTITY_FC_METAL_RH_FIELD'),
			),
			'FC_METAL_IR' => array(
				'data_type' => 'string',
				'validation' => array(__CLASS__, 'validateFcMetalIr'),
				'title' => Loc::getMessage('RELEASE_ENTITY_FC_METAL_IR_FIELD'),
			),
			'FC_METAL_RU' => array(
				'data_type' => 'string',
				'validation' => array(__CLASS__, 'validateFcMetalRu'),
				'title' => Loc::getMessage('RELEASE_ENTITY_FC_METAL_RU_FIELD'),
			),
			'FC_METAL_OS' => array(
				'data_type' => 'string',
				'validation' => array(__CLASS__, 'validateFcMetalOs'),
				'title' => Loc::getMessage('RELEASE_ENTITY_FC_METAL_OS_FIELD'),
			),
			'FC_METAL_AU' => array(
				'data_type' => 'string',
				'validation' => array(__CLASS__, 'validateFcMetalAu'),
				'title' => Loc::getMessage('RELEASE_ENTITY_FC_METAL_AU_FIELD'),
			),
			'FC_METAL_AG' => array(
				'data_type' => 'string',
				'validation' => array(__CLASS__, 'validateFcMetalAg'),
				'title' => Loc::getMessage('RELEASE_ENTITY_FC_METAL_AG_FIELD'),
			),
			'FC_METAL_RE' => array(
				'data_type' => 'string',
				'validation' => array(__CLASS__, 'validateFcMetalRe'),
				'title' => Loc::getMessage('RELEASE_ENTITY_FC_METAL_RE_FIELD'),
			),
			'IS_STORNO' => array(
				'data_type' => 'integer',
				'title' => Loc::getMessage('RELEASE_ENTITY_IS_STORNO_FIELD'),
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
	 * Returns validators for FC_METAL_PT field.
	 *
	 * @return array
	 */
	public static function validateFcMetalPt()
	{
		return array(
			new Main\Entity\Validator\Length(null, 10),
		);
	}
	/**
	 * Returns validators for FC_METAL_PD field.
	 *
	 * @return array
	 */
	public static function validateFcMetalPd()
	{
		return array(
			new Main\Entity\Validator\Length(null, 10),
		);
	}
	/**
	 * Returns validators for FC_METAL_RH field.
	 *
	 * @return array
	 */
	public static function validateFcMetalRh()
	{
		return array(
			new Main\Entity\Validator\Length(null, 10),
		);
	}
	/**
	 * Returns validators for FC_METAL_IR field.
	 *
	 * @return array
	 */
	public static function validateFcMetalIr()
	{
		return array(
			new Main\Entity\Validator\Length(null, 10),
		);
	}
	/**
	 * Returns validators for FC_METAL_RU field.
	 *
	 * @return array
	 */
	public static function validateFcMetalRu()
	{
		return array(
			new Main\Entity\Validator\Length(null, 10),
		);
	}
	/**
	 * Returns validators for FC_METAL_OS field.
	 *
	 * @return array
	 */
	public static function validateFcMetalOs()
	{
		return array(
			new Main\Entity\Validator\Length(null, 10),
		);
	}
	/**
	 * Returns validators for FC_METAL_AU field.
	 *
	 * @return array
	 */
	public static function validateFcMetalAu()
	{
		return array(
			new Main\Entity\Validator\Length(null, 10),
		);
	}
	/**
	 * Returns validators for FC_METAL_AG field.
	 *
	 * @return array
	 */
	public static function validateFcMetalAg()
	{
		return array(
			new Main\Entity\Validator\Length(null, 10),
		);
	}
	/**
	 * Returns validators for FC_METAL_RE field.
	 *
	 * @return array
	 */
	public static function validateFcMetalRe()
	{
		return array(
			new Main\Entity\Validator\Length(null, 10),
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