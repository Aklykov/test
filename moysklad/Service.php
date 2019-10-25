<?php

namespace Zografdd\Moysklad;

/**
 * Класс для работы с сущностью "Услуга"
 *
 * Class Service
 * @package Zografdd\Moysklad
 */
class Service extends Entity
{
	protected static $entity = '/entity/service';

	const ORDER_DELIVERY_XML_ID = 'ORDER_DELIVERY';

	static public function getOrderDelivery()
	{
		return static::getByExternalCode(static::ORDER_DELIVERY_XML_ID);
	}
}