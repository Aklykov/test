<?php

namespace Zografdd\Moysklad;

/**
 * Класс для работы с сущностью "Контрагент"
 *
 * Class Agent
 * @package Zografdd\Moysklad
 */
class Agent extends Entity
{
	protected static $entity = '/entity/counterparty';

	const COMPANY_TYPE_DEFAULT = 'individual';

	/**
	 * Создать из данных СРМ
	 *
	 * @param array $orderCrm
	 * @return Entity
	 */
	static public function createFromRetailCrm($orderCrm)
	{
		$agent = static::getByExternalCode($orderCrm['customer']['id']);

		if ($agent->isSuccess())
		{
			// обновляем контрагента
			$agent = static::update($agent->getId(), [
				'name' => (string) static::getFIO($orderCrm),
				'code' => (string) $orderCrm['customer']['id'],
				'externalCode' => (string) $orderCrm['customer']['id'],
				'phone' => (string) $orderCrm['customer']['phones'][0]['number'],
				'email' => (string) $orderCrm['customer']['email'],
				'actualAddress' => (string) $orderCrm['customer']['address']['text'],
				'companyType' => static::COMPANY_TYPE_DEFAULT,
			]);
		}
		else
		{
			// создаем контрагента
			$agent = static::add([
				'name' => (string) static::getFIO($orderCrm),
				'code' => (string) $orderCrm['customer']['id'],
				'externalCode' => (string) $orderCrm['customer']['id'],
				'phone' => (string) $orderCrm['customer']['phones'][0]['number'],
				'email' => (string) $orderCrm['customer']['email'],
				'actualAddress' => (string) $orderCrm['customer']['address']['text'],
				'companyType' => static::COMPANY_TYPE_DEFAULT,
			]);
		}

		return $agent;
	}

	/**
	 * Получить ФИО из заказа СРМ
	 *
	 * @param array $orderCrm
	 * @return string
	 */
	static private function getFIO($orderCrm=[])
	{
		$arFIO = [];
		if (!empty($orderCrm['customer']['lastName']))
			$arFIO[] = $orderCrm['customer']['lastName'];
		if (!empty($orderCrm['customer']['firstName']))
			$arFIO[] = $orderCrm['customer']['firstName'];
		if (!empty($orderCrm['customer']['patronymic']))
			$arFIO[] = $orderCrm['customer']['patronymic'];

		if (!empty($arFIO))
			return implode(' ', $arFIO);
		else
			return 'unknow';
	}

}