<?php

namespace TestDeliveryCost\Delivery\Strategies;

use TestDeliveryCost\Delivery\Attribute\DeliveryType;
use TestDeliveryCost\Order;

#[DeliveryType('post')]
class PostDeliveryStrategy implements DeliveryStrategyInterface
{
	public function calculate(Order $order): float
	{
		return 150 + $order->getWeight() * 5;
	}
}
