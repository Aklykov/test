<?php

namespace AklykovAO\Bitrix24\Handlers;
use AklykovAO\Bitrix24\Api\Deal;
use AklykovAO\Bitrix24\Api\Product;

class ONCRMDEALUPDATE extends Oncrm
{
	protected static $appToken = '#CENSORED#';

	const STAGE_READY_DELIVERY = 1; // Статус ИД "Сделки" Готов к отправке
	const PROPERTY_SHIPPED = 'UF_CRM_1560003462'; // Свойство "Сделки" Списано со склада
	const PROPERTY_SKLAD = 'UF_CRM_1559727189'; // Свойство "Сделки" Склад
	private static $mapSklad = [ // ИД склада "сделки" => ИД свойства склада "товара"
		52 => 106, // Краснопресненская
		54 => 112, // Балаклавский
		68 => 116, // Красноясрк
	];

	/**
	 * Обработчик
	 *
	 * @param array $params
	 */
	public static function execute($post=[])
	{
		if (static::checkAuth($post) === false)
			return;

		$params = $post['data'];

		$id = (int) $params['FIELDS']['ID'];

		if ($id > 0)
		{
			// Изменяем остаток на складе
			$deal = Deal::getById($id);
			$stageId = $deal->getStatus();
			$isShippedDeal = (bool) $deal->getField(static::PROPERTY_SHIPPED);

			if ($stageId == static::STAGE_READY_DELIVERY && !$isShippedDeal)
			{
				// Списываем остаток со склада
				static::shippedRest($deal);
			}
			else if ($stageId != static::STAGE_READY_DELIVERY && $isShippedDeal)
			{
				// Возвращаем остаток на склада
				static::unShippedRest($deal);
			}
		}
	}

	protected static function shippedRest(Deal $deal)
	{
		// Списываем остатки товаров
		$skladDeal = $deal->getField(static::PROPERTY_SKLAD);
		$skladProductPropertyId = static::$mapSklad[$skladDeal];

		$log ='Обновление сделки "'.$deal->getName().'" ['.$deal->getId().'] списание товаров со склада ' . $skladDeal . "\n";

		$productRows = $deal->getProductRows();
		foreach ($productRows as $productRow)
		{
			$apiProduct = Product::getById($productRow['PRODUCT_ID']);
			$quantity = $apiProduct->getPropertyValue($skladProductPropertyId)['value'];
			$quantity -= $productRow['QUANTITY'];
			$apiProduct->setPropertyValue($skladProductPropertyId, $quantity);
			$log .= 'Списан товар "'.$apiProduct->getName().'" ['.$apiProduct->getId().'] в кол-ве ' . $quantity . "\n";
		}

		// Помечаем сделку как отгруженную
		Deal::update($deal->getId(), [
			'fields' => [static::PROPERTY_SHIPPED => true]
		]);

		static::writeLog($log);
	}

	protected static function unShippedRest(Deal $deal)
	{
		// Возвращаем остатки товаров
		$skladDeal = $deal->getField(static::PROPERTY_SKLAD);
		$skladProductPropertyId = static::$mapSklad[$skladDeal];

		$log ='Обновление сделки "'.$deal->getName().'" ['.$deal->getId().'] возвращение товаров со склада ' . $skladDeal . "\n";

		$productRows = $deal->getProductRows();
		foreach ($productRows as $productRow)
		{
			$apiProduct = Product::getById($productRow['PRODUCT_ID']);
			$quantity = $apiProduct->getPropertyValue($skladProductPropertyId)['value'];
			$quantity += $productRow['QUANTITY'];
			$apiProduct->setPropertyValue($skladProductPropertyId, $quantity);
			static::writeLog('Возвращен товар "'.$apiProduct->getName().'" ['.$apiProduct->getId().'] в кол-ве ' . $quantity) . "\n";
		}

		// Помечаем сделку как неотгруженную
		Deal::update($deal->getId(), [
			'fields' => [static::PROPERTY_SHIPPED => false]
		]);

		static::writeLog($log);
	}

}