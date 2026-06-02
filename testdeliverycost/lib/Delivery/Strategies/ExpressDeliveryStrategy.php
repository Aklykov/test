<?php

namespace TestDeliveryCost\Delivery\Strategies;

use TestDeliveryCost\Delivery\Attribute\DeliveryType;
use TestDeliveryCost\Order;

#[DeliveryType('express')]
class ExpressDeliveryStrategy implements DeliveryStrategyInterface
{
	public function calculate(Order $order): float
	{
		return 1000 + $order->getWeight() * 10;
	}
}
