<?php

namespace TestDeliveryCost\Delivery\Strategies;

use TestDeliveryCost\Delivery\Attribute\DeliveryType;
use TestDeliveryCost\Order;

#[DeliveryType('drone')]
class DroneDeliveryStrategy implements DeliveryStrategyInterface
{
	public function calculate(Order $order): float
	{
		return 5000 + $order->getWeight() * 10;
	}
}
