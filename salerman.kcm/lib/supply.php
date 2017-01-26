<?php
namespace Salerman\Kcm;

use Bitrix\Main,
	Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

/**
 * Class SupplyTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> DOC_ID string(30) optional
 * <li> FC_BP string(30) optional
 * <li> JSON string optional
 * <li> FK_NUM string(10) optional
 * <li> FD_DATE datetime optional
 * <li> FC_BOX string(30) optional
 * <li> FN_MASS_SUP string(30) optional
 * <li> FC_DOC_SUP string(30) optional
 * <li> FN_QUANT int optional
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

class SupplyTable extends Main\Entity\DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'salerman_kcm_supply';
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
				'title' => Loc::getMessage('SUPPLY_ENTITY_ID_FIELD'),
			),
			'DOC_ID' => array(
				'data_type' => 'string',
				'validation' => array(__CLASS__, 'validateDocId'),
				'title' => Loc::getMessage('SUPPLY_ENTITY_DOC_ID_FIELD'),
			),
			'FC_BP' => array(
				'data_type' => 'string',
				'validation' => array(__CLASS__, 'validateFcBp'),
				'title' => Loc::getMessage('SUPPLY_ENTITY_FC_BP_FIELD'),
			),
			'JSON' => array(
				'data_type' => 'text',
				'title' => Loc::getMessage('SUPPLY_ENTITY_JSON_FIELD'),
			),
			'FK_NUM' => array(
				'data_type' => 'string',
				'validation' => array(__CLASS__, 'validateFkNum'),
				'title' => Loc::getMessage('SUPPLY_ENTITY_FK_NUM_FIELD'),
			),
			'FD_DATE' => array(
				'data_type' => 'datetime',
				'title' => Loc::getMessage('SUPPLY_ENTITY_FD_DATE_FIELD'),
			),
			'FC_BOX' => array(
				'data_type' => 'string',
				'validation' => array(__CLASS__, 'validateFcBox'),
				'title' => Loc::getMessage('SUPPLY_ENTITY_FC_BOX_FIELD'),
			),
			'FN_MASS_SUP' => array(
				'data_type' => 'string',
				'validation' => array(__CLASS__, 'validateFnMassSup'),
				'title' => Loc::getMessage('SUPPLY_ENTITY_FN_MASS_SUP_FIELD'),
			),
			'FC_DOC_SUP' => array(
				'data_type' => 'string',
				'validation' => array(__CLASS__, 'validateFcDocSup'),
				'title' => Loc::getMessage('SUPPLY_ENTITY_FC_DOC_SUP_FIELD'),
			),
			'FN_QUANT' => array(
				'data_type' => 'integer',
				'title' => Loc::getMessage('SUPPLY_ENTITY_FN_QUANT_FIELD'),
			),
			'FC_METAL_PT' => array(
				'data_type' => 'string',
				'validation' => array(__CLASS__, 'validateFcMetalPt'),
				'title' => Loc::getMessage('SUPPLY_ENTITY_FC_METAL_PT_FIELD'),
			),
			'FC_METAL_PD' => array(
				'data_type' => 'string',
				'validation' => array(__CLASS__, 'validateFcMetalPd'),
				'title' => Loc::getMessage('SUPPLY_ENTITY_FC_METAL_PD_FIELD'),
			),
			'FC_METAL_RH' => array(
				'data_type' => 'string',
				'validation' => array(__CLASS__, 'validateFcMetalRh'),
				'title' => Loc::getMessage('SUPPLY_ENTITY_FC_METAL_RH_FIELD'),
			),
			'FC_METAL_IR' => array(
				'data_type' => 'string',
				'validation' => array(__CLASS__, 'validateFcMetalIr'),
				'title' => Loc::getMessage('SUPPLY_ENTITY_FC_METAL_IR_FIELD'),
			),
			'FC_METAL_RU' => array(
				'data_type' => 'string',
				'validation' => array(__CLASS__, 'validateFcMetalRu'),
				'title' => Loc::getMessage('SUPPLY_ENTITY_FC_METAL_RU_FIELD'),
			),
			'FC_METAL_OS' => array(
				'data_type' => 'string',
				'validation' => array(__CLASS__, 'validateFcMetalOs'),
				'title' => Loc::getMessage('SUPPLY_ENTITY_FC_METAL_OS_FIELD'),
			),
			'FC_METAL_AU' => array(
				'data_type' => 'string',
				'validation' => array(__CLASS__, 'validateFcMetalAu'),
				'title' => Loc::getMessage('SUPPLY_ENTITY_FC_METAL_AU_FIELD'),
			),
			'FC_METAL_AG' => array(
				'data_type' => 'string',
				'validation' => array(__CLASS__, 'validateFcMetalAg'),
				'title' => Loc::getMessage('SUPPLY_ENTITY_FC_METAL_AG_FIELD'),
			),
			'FC_METAL_RE' => array(
				'data_type' => 'string',
				'validation' => array(__CLASS__, 'validateFcMetalRe'),
				'title' => Loc::getMessage('SUPPLY_ENTITY_FC_METAL_RE_FIELD'),
			),
			'IS_STORNO' => array(
				'data_type' => 'integer',
				'title' => Loc::getMessage('SUPPLY_ENTITY_IS_STORNO_FIELD'),
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
	 * Returns validators for FK_NUM field.
	 *
	 * @return array
	 */
	public static function validateFkNum()
	{
		return array(
			new Main\Entity\Validator\Length(null, 10),
		);
	}
	/**
	 * Returns validators for FC_BOX field.
	 *
	 * @return array
	 */
	public static function validateFcBox()
	{
		return array(
			new Main\Entity\Validator\Length(null, 30),
		);
	}
	/**
	 * Returns validators for FN_MASS_SUP field.
	 *
	 * @return array
	 */
	public static function validateFnMassSup()
	{
		return array(
			new Main\Entity\Validator\Length(null, 30),
		);
	}
	/**
	 * Returns validators for FC_DOC_SUP field.
	 *
	 * @return array
	 */
	public static function validateFcDocSup()
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
				'FK_NUM' => $DOC_NUM,
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