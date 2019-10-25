<?php

namespace Zografdd\Moysklad;
use \GuzzleHttp\Exception\ClientException;

/**
 * Класс для работы с сущностью "Заказ"
 *
 * Class Order
 * @package Zografdd\Moysklad
 */
class Order extends Entity
{
	protected static $entity = '/entity/customerorder';
	protected $orderCrm=[];

	const ATTRIBUTES_ORDER_METHOD_ID = 'c219ee3da-7aea-11e9-9109-f8fc001802fd';

	/**
	 * Зарезирвировать товары заказа
	 */
	public function reserve()
	{
		$this->loadMetaByName('positions');

		$orderId = $this->data['id'];
		foreach ($this->data['positions']['data']['rows'] as $k => $row)
		{
			$positionId = $row['id'];
			$quantity = $row['quantity'];
			$reserve = $row['reserve'];
			if ($quantity != $reserve)
			{
				$url = static::$entity . '/' . $orderId.'/positions/'.$positionId;
				Client::getInstance()->put(
					$url,
					['reserve' => $quantity]
				);
				// Обновляем текущий объект
				$this->data['positions']['data']['rows'][$k]['reserve'] = $quantity;
			}
		}
	}

	/**
	 * РазРезервировать товары заказа
	 */
	public function unReserve()
	{
		$this->loadMetaByName('positions');

		$orderId = $this->data['id'];
		foreach ($this->data['positions']['data']['rows'] as $k => $row)
		{
			$positionId = $row['id'];
			$reserve = $row['reserve'];
			if ($reserve > 0)
			{
				$url = static::$entity . '/' . $orderId.'/positions/'.$positionId;
				Client::getInstance()->put(
					$url,
					['reserve' => 0]
				);

				// Обновляем текущий объект
				$this->data['positions']['data']['rows'][$k]['reserve'] = 0;
			}
		}
	}

	/**
	 * Создать отгрузку
	 */
	public function createDemand()
	{
		$this->loadMetaByName('positions');
		$this->loadMetaByName('demands');

		$data = [];
		$data['customerOrder'] = ['meta' => $this->data['meta']];
		$data['organization'] = $this->data['organization'];
		$data['agent'] = $this->data['agent'];
		$data['store'] = $this->data['store'];
		$data['positions'] = [];
		foreach ($this->data['positions']['data']['rows'] as $row)
		{
			$data['positions'][] = [
				'quantity' => $row['quantity'],
				'price' => $row['price'],
				'discount' => $row['discount'],
				'vat' => $row['vat'],
				'assortment' => $row['assortment'],
				'reserve' => $row['reserve'],
			];
		}
		$data['vatEnabled'] = true;
		$data['vatIncluded'] = true;

		$id = $this->data['demands'][0]['data']['id'];
		if (!empty($id))
			$demand = Demand::update($id, $data);
		else
			$demand = Demand::add($data);

		// Обновляем текущий объект
		if ($demand->isSuccess())
		{
			$this->data['demands'][0]['meta'] = $demand->getMeta()['meta'];
			$this->data['demands'][0]['data'] = $demand->getData();
		}
	}

	/**
	 * Удалить отгрузку
	 */
	public function removeDemand()
	{
		$this->loadMetaByName('demands');

		$id = $this->data['demands'][0]['data']['id'];
		$demand = Demand::getById($id);
		if ($demand->isSuccess())
		{
			$demand->remove();

			// Обновляем текущий объект
			unset($this->data['demands']);
		}
	}

	/**
	 * Создать "Входящий платеж"
	 */
	public function createPaymentIn()
	{
		$this->loadMetaByName('payments');

		$data = [];
		$data['group'] = ['meta' => $this->data['group']['meta']];
		$data['organization'] = ['meta' => $this->data['organization']['meta']];
		$data['agent'] = ['meta' => $this->data['agent']['meta']];
		$data['sum'] = $this->data['sum'];
		$data['operations'] = [
			[
				'meta' => $this->data['meta'],
				'linkedSum' => $this->data['sum'],
			]
		];

		$id = $this->data['payments'][0]['data']['id'];
		if (!empty($id))
			$paymentin = Paymentin::update($id, $data);
		else
			$paymentin = Paymentin::add($data);

		//$paymentin = Paymentin::add($data);
		if ($paymentin->isSuccess())
		{
			$this->data['payments'][0]['meta'] = $paymentin->getMeta()['meta'];
			$this->data['payments'][0]['data'] = $paymentin->getData();
		}
	}

	/**
	 * Удалить "Входящий платеж"
	 */
	public function removePaymentIn()
	{
		$this->loadMetaByName('payments');

		$id = $this->data['payments'][0]['data']['id'];
		$paymentin = Paymentin::getById($id);
		if ($paymentin->isSuccess())
		{
			$paymentin->remove();

			// Обновляем текущий объект
			unset($this->data['payments']);
		}
	}

	/**
	 * Зарезервирован ли заказ
	 * Заказ считается резервированным если все его позиции зарезервированны
	 *
	 * @return bool
	 */
	public function isReserv()
	{
		$this->loadMetaByName('positions');

		foreach ($this->data['positions']['data']['rows'] as $row)
		{
			if ($row['quantity'] != $row['reserve'])
				return false;
		}

		return true;
	}

	/**
	 * Отгружен ли заказ
	 *
	 * @return bool
	 */
	public function isDemand()
	{
		return !empty($this->data['demands']);
	}

	/**
	 * Оплачен ли заказ
	 *
	 * @return bool
	 */
	public function isPaymentIn()
	{
		return !empty($this->data['payments']);
	}

	/**
	 * Отменен ли заказ
	 *
	 * @return bool
	 */
	public function isCancel()
	{
		$this->loadMetaByName('state');

		return $this->data['state']['data']['color'] == State::COLOR_CANCEL;
	}

	/**
	 * Выполнен ли заказ
	 *
	 * @return bool
	 */
	public function isComplete()
	{
		$this->loadMetaByName('state');

		return $this->data['state']['data']['color'] == State::COLOR_COMPLETE;
	}

	/**
	 * Получить сумму заказа
	 *
	 * @return float
	 */
	public function getSum()
	{
		return (float) ($this->data['sum'] / 100);
	}

	/**
	 * Проверка на корректность создания заказа из данных СРМ
	 *
	 * @return bool
	 */
	public function checkOrderPriceFromCrm()
	{
		return (float) $this->orderCrm['totalSumm'] == $this->getSum();
	}

	/**
	 * Получить связанную "Отгрузку" заказа
	 *
	 * @return Demand
	 */
	public function getLinkDemand()
	{
		$data = [];
		$url = $this->data['demands'][0]['meta']['href'];
		if (!empty($url))
		{
			$data = Client::getInstance()->get($url);
		}

		$demand = new Demand($data);
		return $demand;
	}

	/**
	 * Получить связанный "Входящий платеж" заказа
	 *
	 * @return Paymentin
	 */
	public function getLinkPaymentin()
	{
		$data = [];
		$url = $this->data['payments'][0]['meta']['href'];
		if (!empty($url))
		{
			$data = Client::getInstance()->get($url);
		}

		$paymentin = new Paymentin($data);
		return $paymentin;
	}

	/**
	 * Создать из данных СРМ
	 *
	 * @param array $orderCrm
	 * @return static
	 */
	static public function createOrderFromRetailCrm($orderCrm)
	{
		$data = [];

		$data['name'] = $orderCrm['number'];
		$data['code'] = $orderCrm['number'];
		$data['externalCode'] = $orderCrm['number'];
		$data['moment'] = static::getMomentFromRetailCrm($orderCrm);
		$data['description'] = static::getDescriptionFromRetailCrm($orderCrm);
		$data['attributes'] = static::getAttributesFromRetailCrm($orderCrm);

		// Получаем организацию
		$data['group'] = Group::getDefaultEntity()->getMeta();

		// Получаем организацию
		$data['organization'] = Organization::getDefaultEntity()->getMeta();

		// Получаем статус
		$data['state'] = State::createFromRetailCrm($orderCrm)->getMeta();

		// Получаем склад
		$data['store'] = Store::createFromRetailCrm($orderCrm)->getMeta();

		// Получаем контрагента
		$data['agent'] = Agent::createFromRetailCrm($orderCrm)->getMeta();

		// Получаем позиции товаров
		$data['positions'] = Positions::createFromRetailCrm($orderCrm);

		$order = static::getByExternalCode($orderCrm['number']);
		if ($order->isSuccess())
		{
			// Обновляем заказ
			$order = static::update($order->getId(), $data);
		}
		else
		{
			// Создаем заказ
			$order = static::add($data);
		}

		$order->orderCrm = $orderCrm;
		$order->loadMetaByName('positions');
		$order->loadMetaByName('state');

		// Пост-обработка заказа
		static::postLogic($order);

		return $order;
	}

	/**
	 * Получить описание из данных СРМ
	 *
	 * @param array $orderCrm
	 * @return string
	 */
	static private function getDescriptionFromRetailCrm($orderCrm=[])
	{
		$description = '';
		if ($orderCrm['customerComment'])
			$description .= "КОММЕНТАРИЙ КЛИЕНТА:\n" . $orderCrm['customerComment'] . "\n\n";
		if ($orderCrm['managerComment'])
			$description .= "КОММЕНТАРИЙ ОПЕРАТОРА:\n" . $orderCrm['managerComment'] . "\n\n";

		return $description;
	}

	/**
	 * Получить дату заказа из данных СРМ
	 *
	 * @param array $orderCrm
	 * @return string
	 */
	static private function getMomentFromRetailCrm($orderCrm=[])
	{
		// Так-как МС по непонятной причине плюсует 3ч, то приходится отнимать их перед отправкой данных
		$moment = date('Y-m-d H:i:s', strtotime($orderCrm['createdAt']) - 10800);
		return $moment;
	}

	/**
	 * Получить аттрибуты заказа из данных СРМ
	 *
	 * @param array $orderCrm
	 * @return array
	 */
	static private function getAttributesFromRetailCrm($orderCrm=[])
	{
		\Bitrix\Main\Loader::includeModule('sale');

		$attributes = [];

		// Способ заказа
		$method = \Bitrix\Sale\Internals\OrderPropsVariantTable::getRow([
			'select' => ['NAME'],
			'filter' => ['VALUE' => $orderCrm['orderMethod']],
		])['NAME'];
		$method = !empty($method) ? $method : $orderCrm['orderMethod'];

		$attributes[] = [
			'id' => static::ATTRIBUTES_ORDER_METHOD_ID,
			'value' => [
				'name' => $method
			],
		];

		return $attributes;
	}

	static private function postLogic(\Zografdd\Moysklad\Order $order)
	{
		if ($order->isCancel())
		{
			// снимаем резерв
			$order->unReserve();

			// удаляем отгрузку
			if ($order->isDemand())
				$order->removeDemand();

			// удаляем оплату
			if ($order->isPaymentIn())
				$order->removePaymentIn();
		}
		else
		{
			// Все статусы кроме отмены идут в резерв
			if ($order->isReserv() === false)
				$order->reserve();

			if ($order->isComplete())
			{
				// Для выполненого заказа создаем/обновляем отгрузку
				$order->createDemand();

				// Для выполненого заказа создаем оплату
				$order->createPaymentIn();
			}
			else
			{
				// для невыполненного удаляем отгрузку (если есть)
				if ($order->isDemand())
					$order->removeDemand();

				// для невыполненного удаляем оплату (если есть)
				if ($order->isPaymentIn())
					$order->removePaymentIn();
			}
		}
	}

	/**
	 * Получить заказ из СРМ по api v5
	 *
	 * @param string $id
	 * @param string $by
	 * @param string $siteId
	 *
	 * @return array
	 */
	static public function getOrderFromRetailCrmV5($id='', $by='externalId', $siteId='bs')
	{
		\Bitrix\Main\Loader::includeModule('intaro.retailcrm');
		$apiKey = \Bitrix\Main\Config\Option::get(\RetailCrmOrder::$MODULE_ID, \RetailCrmOrder::$CRM_API_KEY_OPTION, 0);

		$urlTemplate = 'https://i-lux.retailcrm.ru/api/v5/orders/#ORDER#?apiKey=#KEY#&by=#BY#&site=#SITE#';
		$url = str_replace(['#ORDER#', '#KEY#', '#BY#', '#SITE#'], [$id, $apiKey, $by, $siteId], $urlTemplate);

		try
		{
			$client = new \GuzzleHttp\Client();
			$request = $client->request(
				'GET',
				$url
			);
			$orderCrm = json_decode($request->getBody()->getContents(), true);
		}
		catch (ClientException $e)
		{
			$orderCrm = json_decode($e->getResponse()->getBody()->getContents(), true);
		}

		return $orderCrm;
	}

}
