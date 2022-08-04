<?php
namespace Ibs\StoreNotebook\Entity;

use Bitrix\Main;
use Bitrix\Main\{
	Localization\Loc,
	Type\DateTime};
use Bitrix\Main\ORM\Fields\{
	DatetimeField,
	IntegerField,
	ScalarField,
	StringField,
	Relations\Reference,
	Validators\LengthValidator};
use Bitrix\Main\ORM\Data\DataManager;

Loc::loadMessages(__FILE__);

/**
 * Class OptionLinkModelTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> MODEL_ID int mandatory
 * <li> OPTION_ID int mandatory
 * </ul>
 *
 * @package \Ibs\StoreNotebook\Entity
 **/
class OptionLinkModelTable extends Main\Entity\DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'ibs_storenotebook_option_link_model';
	}

	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap()
	{
		return [
			'ID' => new IntegerField(
				'ID',
				[
					'primary' => true,
					'autocomplete' => true,
					'title' => Loc::getMessage('IBS_STORENOTEBOOK_OPTION_LINK_MODEL_FIELD_ID')
				]
			),
			'MODEL_ID' => new IntegerField(
				'MODEL_ID',
				[
					'required' => true,
					'title' => Loc::getMessage('IBS_STORENOTEBOOK_OPTION_LINK_MODEL_FIELD_MODEL_ID')
				]
			),
			'OPTION_ID' => new IntegerField(
				'OPTION_ID',
				[
					'required' => true,
					'title' => Loc::getMessage('IBS_STORENOTEBOOK_OPTION_LINK_MODEL_FIELD_OPTION_ID')
				]
			),
			'MODEL' => new Reference(
				'MODEL',
				'Ibs\StoreNotebook\Entity\Model',
				['=this.MODEL_ID' => 'ref.ID'],
				['join_type' => 'LEFT']
			),
			'OPTION' => new Reference(
				'OPTION',
				'Ibs\StoreNotebook\Entity\Option',
				['=this.OPTION_ID' => 'ref.ID'],
				['join_type' => 'LEFT']
			),
		];
	}
}