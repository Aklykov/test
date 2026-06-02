<?php

namespace TestDeliveryCost\Delivery\Strategies;

use TestDeliveryCost\Delivery\Attribute\DeliveryType;
use TestDeliveryCost\Order;

#[DeliveryType('courier')]
class CourierDeliveryStrategy implements DeliveryStrategyInterface
{
	public function calculate(Order $order): float
	{
		return 300 + $order->getWeight() * 10;
	}
}
