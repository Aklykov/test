<?php

namespace Aklykov\Gamecode;
use \Bitrix\Main\Loader;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Config\Configuration;
use \Bitrix\Highloadblock\HighloadBlockTable;
use \Bitrix\Main\Localization\Loc as Loc;
Loc::loadMessages(__FILE__);

/**
 * Класс описывающий сущность "Серийный номер"
 *
 * Class Gamecode
 * @package Aklykov\Gamecode
 */
class Gamecode
{

	const MODULE_ID = 'aklykov.gamecode'; // ИД Модуля

	/**
	 * ИД записи
	 * @var int
	 */
	public $id;

	/**
	 * Серийный номер
	 * @var string
	 */
	public $code;

	/**
	 * Номер заказа
	 * @var string
	 */
	public $order;

	/**
	 * Email заказа
	 * @var string
	 */
	public $email;

	public static $LANG_entity = false;

	/**
	 * Получить сущность
	 *
	 * @return \Bitrix\Main\Entity\Base
	 */
	static public function GetEntity()
	{
		if(self::$LANG_entity === false)
		{
			Loader::includeModule('highloadblock');
			$arConfigSettings = Configuration::getInstance()->getValue(self::MODULE_ID);
			$rsData = HighloadBlockTable::getList(array('filter'=>array('ID' => $arConfigSettings['HLBLOCK_GAMECODE'])));
			$arData = $rsData->fetch();
			self::$LANG_entity = HighloadBlockTable::compileEntity($arData);
		}
		return self::$LANG_entity;
	}

	/**
	 * Сохранить запись
	 *
	 * @return Gamecode
	 */
	public function save()
	{
		$obj = self::getByCode($this->code);
		if(is_object($obj))
		{
			// Если есть уже такой серийный номер в таблице - то обновляем эту запись
			$this->id = $obj->id;
			return $this->update();
		}
		else
		{
			// Если нет такого серийного номера в таблице - то создаем новую запись
			return $this->add();
		}
	}

	/**
	 * Создать запись
	 *
	 * @return Gamecode
	 */
	public function add()
	{
		$LANG_entity = self::GetEntity();
		$LANG_entity_data_class = $LANG_entity->getDataClass();
		$arBxData = array(
			'UF_CODE' => $this->code,
			'UF_ORDER' => $this->order,
			'UF_EMAIL' => $this->email
		);

		// Вызываем обработчики события "До создания записи"
		foreach(GetModuleEvents(self::MODULE_ID, 'OnBeforeAddGamecode', true) as $arEvent)
			ExecuteModuleEventEx($arEvent, Array($arBxData));

		$result = $LANG_entity_data_class::add($arBxData);
		$this->id = $result->getId();
		$arBxData['ID'] = $this->id;

		// Вызываем обработчики события "После создания записи"
		foreach(GetModuleEvents(self::MODULE_ID, 'OnAfterAddGamecode', true) as $arEvent)
			ExecuteModuleEventEx($arEvent, Array($arBxData));

		return $this;

	}

	/**
	 * Обновить запись
	 *
	 * @return Gamecode
	 */
	public function update()
	{
		if(!empty($this->id))
		{
			$LANG_entity = self::GetEntity();
			$LANG_entity_data_class = $LANG_entity->getDataClass();
			$arBxData = array(
				'UF_CODE' => $this->code,
				'UF_ORDER' => $this->order,
				'UF_EMAIL' => $this->email
			);

			// Вызываем обработчики события "До обновления записи"
			foreach(GetModuleEvents(self::MODULE_ID, 'OnBeforeUpdateGamecode', true) as $arEvent)
				ExecuteModuleEventEx($arEvent, Array($arBxData));

			$result = $LANG_entity_data_class::update($this->id, $arBxData);
			$arBxData['ID'] = $this->id;

			// Вызываем обработчики события "После обновления записи"
			foreach(GetModuleEvents(self::MODULE_ID, 'OnAfterUpdateGamecode', true) as $arEvent)
				ExecuteModuleEventEx($arEvent, Array($arBxData));

			return $this;
		}
	}

	/**
	 * Удаление записи
	 */
	public function delete()
	{
		if(!empty( $this->id))
		{
			$LANG_entity = self::GetEntity();
			$LANG_entity_data_class = $LANG_entity->getDataClass();

			// Вызываем обработчики события "До удаления записи"
			foreach(GetModuleEvents(self::MODULE_ID, 'OnBeforeDeleteGamecode', true) as $arEvent)
				ExecuteModuleEventEx($arEvent, Array($this));

			$LANG_entity_data_class::delete($this->id);

			// Вызываем обработчики события "После удаления записи"
			foreach(GetModuleEvents(self::MODULE_ID, 'OnAfterDeleteGamecode', true) as $arEvent)
				ExecuteModuleEventEx($arEvent, Array($this));
		}
	}

	/**
	 * Отправка письма
	 */
	public function sendEmail()
	{
		$arSites = array();
		$sites = \CSite::GetList(($b=''), ($o=''), Array());
		while ($site = $sites->Fetch())
			$arSites[] = $site['LID'];

		$arEventFields = array(
			'GAMECODE' => $this->code,
			'EMAIL_TO' => $this->email
		);
		\CEvent::Send('AKLYKOV_GAMECODE', $arSites, $arEventFields);
	}

	/**
	 * Получить список записей
	 *
	 * @param array $arFilter
	 * @param array $arOrder
	 * @param int $limit
	 * @param int $offset
	 * @return Gamecode[]
	 */
	public static function getList($arFilter=array(), $arOrder=array('ID' => 'ASC'), $limit=0, $offset=0)
	{
		$LANG_entity = self::GetEntity();
		$main_query = new \Bitrix\Main\Entity\Query($LANG_entity);
		$main_query->setSelect(array('*'));
		$main_query->setFilter($arFilter);
		$main_query->setOrder($arOrder);
		$main_query->setLimit($limit);
		$main_query->setOffset($offset);
		$result = $main_query->exec();
		$result = new \CDBResult($result);
		$arObj = array();
		while ($row = $result->Fetch())
		{
			$obj = new self();
			$obj->id = $row['ID'];
			$obj->code = $row['UF_CODE'];
			$obj->order = $row['UF_ORDER'];
			$obj->email = $row['UF_EMAIL'];
			$arObj[] = $obj;
		}
		return $arObj;
	}

	/**
	 * Получить запись по ИД
	 *
	 * @param int $id
	 * @return Gamecode
	 */
	public static function getByID($id)
	{
		$arFilter = array('=ID' => $id);
		$arOrder = array('ID' => 'ASC');
		$limit = 1;
		$offset = 0;
		$arObj = self::GetList($arFilter, $arOrder, $limit, $offset);
		return current($arObj);
	}

	/**
	 * Получить запись по Серийному номеру
	 *
	 * @param string $code
	 * @return Gamecode
	 */
	public static function getByCode($code)
	{
		$arFilter = array('=UF_CODE' => $code);
		$arOrder = array('ID' => 'ASC');
		$limit = 1;
		$offset = 0;
		$arObj = self::GetList($arFilter, $arOrder, $limit, $offset);
		return current($arObj);
	}

	/**
	 * Получить запись без привязки к заказу
	 *
	 * @return Gamecode
	 */
	public static function getNewCode()
	{
		$arFilter = array('UF_ORDER' => false);
		$arOrder = array('ID' => 'ASC');
		$limit = 1;
		$offset = 0;
		$arObj = self::GetList($arFilter, $arOrder, $limit, $offset);
		return current($arObj);
	}

}