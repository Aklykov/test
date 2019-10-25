<?php

namespace Zografdd\Moysklad;
use \Zografdd\BlueSleep\TypeProduct;

/**
 * Класс для работы с позициями Заказа
 *
 * Class Positions
 * @package Zografdd\Moysklad
 */
class Positions
{
	/**
	 * Создать из данных СРМ
	 *
	 * @param array $orderCrm
	 * @return array
	 */
	static public function createFromRetailCrm($orderCrm=[])
	{
		$positions = [];
		// Заменяем товары-комплекты на комплектующие
		$orderItems = static::replaceComplectFromRetailCrm($orderCrm);
		foreach ($orderItems as $orderItem)
		{
			$xmlId = !empty($orderItem['offer']['xmlId']) ? $orderItem['offer']['xmlId'] : $orderItem['offer']['externalId'];
			$externalCode = end(explode('#', $xmlId));

			$variant = Variant::getByExternalCode($externalCode);
			if ($variant->isSuccess())
			{
				$item = [];
				$item['quantity'] = floatVal($orderItem['quantity']);
				$item['price'] = floatVal($orderItem['initialPrice'] * 100);
				$item['discount'] = static::getDiscountFromRetailCrm($orderItem);
				$item['vat'] = 20;
				$item['assortment'] = $variant->getMeta();

				$positions[] = $item;
			}
		}

		// Если есть доставка то добавляем ее как услугу
		if ($orderCrm['delivery']['cost'] > 0)
			$positions[] = static::getPositionOrderDelivery($orderCrm['delivery']['cost']);

		return $positions;
	}

	/**
	 * Получить размер скидки в % для товара из данных СРМ
	 *
	 * @param array $orderItem
	 * @return float
	 */
	static private function getDiscountFromRetailCrm($orderItem=[])
	{
		$discount = 0;

		if ($orderItem['discountTotal'] > 0)
		{
			// абсолютная скидка, учитывает в себе все варианты скидок как на товар так и на заказ (api v5)
			$discount = round( (floatVal($orderItem['discountTotal']) / floatVal($orderItem['initialPrice'])), 8 ) * 100;
		}
		else if ($orderItem['discount'] > 0 && $orderItem['discountPercent'] > 0)
		{
			// абсолютная скидка и процентная скидка (api v4)
			$pricePercent = (floatVal(100 - $orderItem['discountPercent']) / 100);
			$discountPrice = $pricePercent * floatVal($orderItem['initialPrice']);
			$discountPrice = $orderItem['initialPrice'] - ($discountPrice - $orderItem['discount']);
			$discount = round( (floatVal($discountPrice) / floatVal($orderItem['initialPrice'])), 8 ) * 100;
		}
		else if ($orderItem['discount'] > 0 && empty($orderItem['discountPercent']))
		{
			// абсолютная скидка (api v4)
			$discount = round( (floatVal($orderItem['discount']) / floatVal($orderItem['initialPrice'])), 8 ) * 100;
		}
		else if ($orderItem['discountPercent'] > 0 && empty($orderItem['discount']))
		{
			// процентная скидка (api v4)
			$discount = floatVal($orderItem['discountPercent']);
		}

		return $discount;
	}

	/**
	 * Заменить комплекты на товары из данных СРМ
	 *
	 * @param array $orderCrm
	 * @return array
	 */
	static private function replaceComplectFromRetailCrm($orderCrm=[])
	{
		$orderCrmItems = [];
		foreach ($orderCrm['items'] as $orderItem)
		{
			$orderItems = static::replaceComplectForProductItem($orderItem);
			$orderCrmItems = array_merge($orderCrmItems, $orderItems);
		}

		return $orderCrmItems;
	}

	/**
	 * Заменить товар-комплект на товары-комплектующие комплекта (работает рекурсивно)
	 *
	 * @param array $orderCrmItem
	 * @return array
	 */
	static private function replaceComplectForProductItem($orderCrmItem=[])
	{
		\Bitrix\Main\Loader::includeModule('iblock');
		\Bitrix\Main\Loader::includeModule('catalog');

		#$complectId = $orderCrmItem['offer']['externalId'];
		$complectXmlId = $orderCrmItem['offer']['xmlId'];
		$priceComplectDiscount = $orderCrmItem['initialPrice'] - $orderCrmItem['discountTotal'];

		// Запрашиваем комплект (ТП) с его комплектующими (ТП)
		$rsOffer = \CIBlockElement::GetList(
			['CATALOG_PRICE_'.BASE_PRICE_ID => 'ASC'],
			[
				#'ID' => $complectId,
				'XML_ID' => $complectXmlId,
				'IBLOCK_ID' => \EndorphinConfig::getInstance()->getIblockId__Offers(),
				'PROPERTY_TYPE_PRODUCT' => [TypeProduct::COMPLECT, TypeProduct::BED],
			],
			false,
			false,
			[]
		);
		if ($objOffer = $rsOffer->GetNextElement())
		{
			$arOffer = $objOffer->GetFields();
			$arOffer['PROPERTIES'] = $objOffer->GetProperties();

			$haveMatrassInComplect = false; // в комплекте имеется матрас?
			$isComplectGift = ($priceComplectDiscount == 0); // комплект является подарком? (100% скидки)

			$priceComplectBase = (int) $arOffer['PROPERTIES']['COMP_BASE_PRICE']['VALUE'];
			$discountComplect = $priceComplectBase - $priceComplectDiscount;
			$discountPercent = 1 - ($priceComplectDiscount / $priceComplectBase);

			// Запрашиваем комплектующие
			$arComplectOffers = [];
			foreach ($arOffer['PROPERTIES']['COMP_OFFERS']['VALUE'] as $k => $offerId)
			{
				// Запрашиваем подробно каждый комплектующий
				$rsOfferComplect = \CIBlockElement::GetList(
					[],
					[
						'IBLOCK_ID' => \EndorphinConfig::getInstance()->getIblockId__Offers(),
						'ID' => $offerId
					],
					false,
					['nTopCount' => 1],
					['ID', 'NAME', 'XML_ID', 'PROPERTY_TYPE_PRODUCT', 'CATALOG_GROUP_'.BASE_PRICE_ID]
				);
				if ($arOfferComplect = $rsOfferComplect->GetNext())
				{
					$typeProduct = $arOfferComplect['PROPERTY_TYPE_PRODUCT_VALUE'];
					$countInComplect = $arOffer['PROPERTIES']['COMP_COUNTS']['VALUE'][$k]; // кол-во товара в комплекте
					$arComplectOffers[] = [
						'TYPE_PRODUCT' => $typeProduct,
						'NAME' => $arOfferComplect['NAME'],
						'PRODUCT_XML_ID' => $arOfferComplect['XML_ID'],
						'QUANTITY' => $countInComplect,
						'BASE_PRICE' => $arOfferComplect['CATALOG_PRICE_'.BASE_PRICE_ID],
						'PRICE' => 0,
						'DISCOUNT_PRICE' => 0,
					];
				}

				if (in_array($typeProduct, [TypeProduct::MATRAS, TypeProduct::MATRAS_CONCEPT]))
				{
					// Логика рассчета для матраса активируем только в том случае,
					// если цена матраса больше или равна скидке комплекта, иначе улетаем в минус
					$priceMatras = $arOfferComplect['CATALOG_PRICE_'.BASE_PRICE_ID];
					if ($priceMatras >= $discountComplect)
						$haveMatrassInComplect = true;
				}
			}

			$newComplectDiscount = 0;
			foreach ($arComplectOffers as &$arComplect)
			{
				if ($isComplectGift) // Логика для комплектов-подарков (на все товары 100% скидка)
				{
					$arComplect['PRICE'] = 0;
					$arComplect['DISCOUNT_PRICE'] = $arComplect['BASE_PRICE'];
				}
				else if ($haveMatrassInComplect) // Логика для комплектов с матрасом (даем скидку только на матрас)
				{
					if (in_array($arComplect['TYPE_PRODUCT'], [TypeProduct::MATRAS, TypeProduct::MATRAS_CONCEPT]))
					{
						$arComplect['PRICE'] = $arComplect['BASE_PRICE'] - $discountComplect;
						$arComplect['DISCOUNT_PRICE'] = $discountComplect;
					}
					else
					{
						$arComplect['PRICE'] = $arComplect['BASE_PRICE'];
						$arComplect['DISCOUNT_PRICE'] = 0;
					}
				}
				else // Логика для комплектов без матрасов (даем скидку на все пропорционально)
				{
					$arComplect['PRICE'] = round($arComplect['BASE_PRICE'] * (1 - $discountPercent));
					$arComplect['DISCOUNT_PRICE'] = $arComplect['BASE_PRICE'] - $arComplect['PRICE'];
				}

				$newComplectDiscount += $arComplect['PRICE'] * $arComplect['QUANTITY'];
			}
			unset($arComplect);

			// Проверка суммы товаров (вдруг поделилось неправильно)
			$diff = $priceComplectDiscount - $newComplectDiscount;
			if ($diff != 0)
			{
				array_multisort(
					array_column($arComplectOffers, 'QUANTITY'),
					SORT_ASC,
					$arComplectOffers
				);
				$arComplectOffers[0]['PRICE'] += ($diff / $arComplectOffers[0]['QUANTITY']);
				$arComplectOffers[0]['DISCOUNT_PRICE'] -= ($diff / $arComplectOffers[0]['QUANTITY']);
			}

			// Приводим к СРМ виду
			$arComplectOffersFormat = [];
			foreach ($arComplectOffers as $arComplect)
			{
				$arComplectOfferFormat = [
					'NAME' => $arComplect['NAME'],
					'TYPE_PRODUCT' => $arComplect['TYPE_PRODUCT'],
					'discount' => $arComplect['DISCOUNT_PRICE'],
					'initialPrice' => $arComplect['BASE_PRICE'],
					'discountTotal' => 0,
					'quantity' => $arComplect['QUANTITY'] * $orderCrmItem['quantity'],
					'offer' => [
						'xmlId' => $arComplect['PRODUCT_XML_ID'],
					],
				];
				// Если комплектующее комплекта сам является комплектом, то рекурсивно разбиваем его дальше на части
				if (in_array($arComplectOfferFormat['TYPE_PRODUCT'], [TypeProduct::COMPLECT, TypeProduct::BED]))
				{
					$arComplectOfferFormat['discountTotal'] = $arComplectOfferFormat['discount'];
					$arComplectOfferFormat['discount'] = 0;

					$arSubComplectOffers = static::replaceComplectForProductItem($arComplectOfferFormat);
					$arComplectOffersFormat = array_merge($arComplectOffersFormat, $arSubComplectOffers);
				}
				else
				{
					$arComplectOffersFormat = array_merge($arComplectOffersFormat, [$arComplectOfferFormat]);
				}
			}

			return $arComplectOffersFormat;
		}
		else
		{
			return [$orderCrmItem];
		}
	}

	/**
	 * Получить доставку как услугу "МойСклад"
	 *
	 * @param int $deliveryPrice
	 * @return array
	 */
	static private function getPositionOrderDelivery($deliveryPrice=0)
	{
		$delivery = Service::getOrderDelivery();

		return [
			'quantity' => 1,
			'price' => (float) $deliveryPrice * 100,
			'discount' => 0,
			'vat' => 20,
			'assortment' => $delivery->getMeta(),
		];
	}

}