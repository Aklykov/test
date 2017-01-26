<?php

namespace Aklykov\Gamecode;
use \Bitrix\Main\Loader;
use \Bitrix\Main\Config\Configuration;
use \Bitrix\Main\Localization\Loc as Loc;
Loc::loadMessages(__FILE__);

class Order
{

	/**
	 * @var int
	 */
	public $id;

	/**
	 * @var int
	 */
	public $personTypeId;

	/**
	 * @var string
	 */
	public $email;

	/**
	 * Конструктор заказа
	 *
	 * @param int $orderId
	 */
	public function __construct($orderId)
	{
		Loader::includeModule('sale');
		// Получаем тип плательщика заказа
		$params = array(
			'select' => array('ID', 'PERSON_TYPE_ID'),
			'filter' => array('ID' => $orderId),
			'limit' => 1
		);
		$rsOrder = \Bitrix\Sale\OrderTable::getList($params);
		if($arOrder = $rsOrder->Fetch())
		{
			$this->id = $arOrder['ID'];
			$this->personTypeId = $arOrder['PERSON_TYPE_ID'];

			// Получаем свойства заказа Email
			$arPropsOrder = array();
			$arFilter = array(
				'ORDER_ID' => $orderId,
				'IS_EMAIL' => true
			);
			$rsPropsOrder = \CSaleOrderPropsValue::GetList(array(), $arFilter);
			if($arPropOrder = $rsPropsOrder->Fetch())
			{
				$this->email = $arPropOrder['VALUE'];
			}
		}
		else
		{
			global $APPLICATION;
			$APPLICATION->ThrowException('Заказ не найден');
		}
	}

	/**
	 * Привязать к заказу "Серийный номер"
	 *
	 * @param string $gamecode
	 */
	public function linkGamecode($gamecode)
	{
		Loader::includeModule('sale');

		$arConfigSettings = Configuration::getInstance()->getValue(Gamecode::MODULE_ID);
		$arFilterProps = array(
			'ID' => $arConfigSettings['PROPS'],
			'PERSON_TYPE_ID' => $this->personTypeId
		);
		$arSelectProps = array('ID', 'NAME', 'CODE', 'VALUE');
		$rsProps = \CSaleOrderProps::GetList(array(), $arFilterProps, false, false, $arSelectProps);
		if($arProps = $rsProps->Fetch())
		{
			// Привязываем "Серийный номер" к заказу
			$arFieldsPropsValue = array(
				'ORDER_ID' => $this->id,
				'ORDER_PROPS_ID' => $arProps['ID'],
				'NAME' => $arProps['NAME'],
				'CODE' => $arProps['CODE'],
				'VALUE' => $gamecode
			);
			// Проверяем заполнено ли уже такое поле (да - обновляем, нет - создаем)
			$arFilterPropsValue = array(
				'ORDER_ID' => $this->id,
				'ORDER_PROPS_ID' => $arProps['ID'],
				'NAME' => $arProps['NAME'],
				'CODE' => $arProps['CODE']
			);
			$arSelectPropsValue = array('ID');
			$rsPropsValue = \CSaleOrderPropsValue::GetList(array(), $arFilterPropsValue, false, false, $arSelectPropsValue);
			if($arPropsValue = $rsPropsValue->Fetch())
			{
				d($arPropsValue);
				\CSaleOrderPropsValue::Update($arPropsValue['ID'], $arFieldsPropsValue);
			}
			else
			{
				\CSaleOrderPropsValue::Add($arFieldsPropsValue);
			}
		}
	}

	/**
	 * Отправить код по заказу
	 *
	 * @param int $orderId
	 */
	public static function sendCodeByOrder($orderId)
	{
		// Получаем заказ
		$objOrder = new Order($orderId);

		// Получаем запись без привязки к заказу
		$objGamecode = Gamecode::getNewCode();

		// Привязываем заказ к записи
		$objGamecode->order = $objOrder->id;
		$objGamecode->email = $objOrder->email;
		$objGamecode->update();

		// Привязываем запись к заказу
		$objOrder->linkGamecode($objGamecode->code);

		// Отправляем письмо
		$objGamecode->sendEmail();
	}

	/**
	 * Обработчик события "Оплата заказа"
	 *
	 * @param $ORDER_ID
	 * @param $eventName
	 * @param $arFields
	 * @param $status
	 */
	public static function OnOrderStatusSendEmailHandler($ORDER_ID, &$eventName, &$arFields, $status)
	{
		// Если заказ был оплачен, отправляем "Серийный номер игры"
		if($status == 'P')
		{
			self::sendCodeByOrder($ORDER_ID);
		}
	}


}