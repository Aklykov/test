<?php

namespace TestDeliveryCost\Delivery\Strategies;

use TestDeliveryCost\Delivery\Attribute\DeliveryType;
use TestDeliveryCost\Order;

#[DeliveryType('pickup')]
class PickupDeliveryStrategy implements DeliveryStrategyInterface
{
	public function calculate(Order $order): float
	{
		return 0;
	}
}
