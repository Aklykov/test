<?php

namespace Zografdd\Moysklad;

/**
 * Класс для работы с сущностью "Статус Заказа"
 *
 * Class State
 * @package Zografdd\Moysklad
 */
class State extends Entity
{
	protected static $entity = '/entity/customerorder/metadata/states';

	const COLOR_NEW = 16703150;
	const COLOR_APPROVAL = 15920553;
	const COLOR_ASSEMBLING = 14084519;
	const COLOR_DELIVERY = 13233634;
	const COLOR_COMPLETE = 13623783;
	const COLOR_CANCEL = 16373462;
	const TYPE_DEFAULT = 'Regular';

	/**
	 * Создать из данных СРМ
	 *
	 * @param array $orderCrm
	 * @return Entity
	 */
	static public function createFromRetailCrm($orderCrm)
	{
		$statusCrm = static::getStatusFromRetailCrmByCode($orderCrm['status']);
		$state = static::getByName($statusCrm['name']);
		if (!$state->isSuccess())
		{
			// Если такого статуса нет в МС, то создаем его
			$state = static::add([
				'name' => $statusCrm['name'],
				'color' => $statusCrm['color'],
				'stateType' => static::TYPE_DEFAULT,
			]);
		}

		return $state;
	}

	static private function getStatusFromRetailCrmByCode($statusCode='')
	{
		// Цвета групп хардкорим
		$statusesColorCrm = [
			'new' => static::COLOR_NEW, // #fedeae
			'approval' => static::COLOR_APPROVAL, // #f2eda9
			'assembling' => static::COLOR_ASSEMBLING, // #d6e9a7
			'delivery' => static::COLOR_DELIVERY, // #c9ede2
			'complete' => static::COLOR_COMPLETE, // #cfe1e7
			'cancel' => static::COLOR_CANCEL, // #f9d6d6
		];

		// Запрашиваем Статусы из RetailCRM
		$url = \Bitrix\Main\Config\Option::get(\RetailCrmOrder::$MODULE_ID, \RetailCrmOrder::$CRM_API_HOST_OPTION, 0);
		$apiKey = \Bitrix\Main\Config\Option::get(\RetailCrmOrder::$MODULE_ID, \RetailCrmOrder::$CRM_API_KEY_OPTION, 0);
		$api = new \RetailCrm\ApiClient($url, $apiKey);
		$statusesCrm = $api->statusesList()['statuses'];

		$statusCrm = $statusesCrm[$statusCode];
		$statusCrm['color'] = $statusesColorCrm[$statusCrm['group']];

		return $statusCrm;
	}

	/**
	 * Получить сущность по NAME (переопределяем родительский метод)
	 *
	 * @param string $name
	 * @return Entity
	 */
	static public function getByName($name='')
	{
		$statesMS = static::getListFromMS();

		$data = $statesMS[$name];
		$entity = new static($data);

		return $entity;
	}

	/**
	 * Получить список статусов заказов из Мой Склад
	 * Статусы заказа не являются полноценной сущностью и их нельзя фильтровать по name или externalCode
	 *
	 * @return array
	 */
	static private function getListFromMS()
	{
		$states = [];
		if (empty(static::$cache['states']))
		{
			$data = Client::getInstance()->get('/entity/customerorder/metadata');
			foreach ($data['states'] as $state)
				$states[$state['name']] = $state;

			static::$cache['states'] = $states;
		}
		else
		{
			$states = static::$cache['states'];
		}

		return $states;
	}
}