<?php

namespace Zografdd\Moysklad;

/**
 * Класс для работы с сущностью "Склад"
 *
 * Class Store
 * @package Zografdd\Moysklad
 */
class Store extends Entity
{
	protected static $entity = '/entity/store';
	protected static $defaultId = 'd9e37b56-458d-11e9-9109-f8fc00122ade';

	const DEFAULT_CODE = 'showrum';

	/**
	 * Создать из данных СРМ
	 *
	 * @param array $orderCrm
	 * @return Entity
	 */
	static public function createFromRetailCrm($orderCrm)
	{
		$code = $orderCrm['customFields']['sklad'];
		$code = !empty($code) ? $code : static::DEFAULT_CODE;

		$store = static::getByExternalCode($code);
		if (!$store->isSuccess())
		{
			// Если такого склада нет в МС, то создаем его
			$store = static::add([
				'name' => $code,
				'code' => $code,
				'externalCode' => $code,
			]);
		}

		return $store;
	}
}