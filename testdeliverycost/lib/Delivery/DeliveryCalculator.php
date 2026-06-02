<?php

namespace TestDeliveryCost\Delivery;

use TestDeliveryCost\Order;

class DeliveryCalculator
{
	public function cost(Order $order, string $type): float
	{
		$strategy = DeliveryStrategyFactory::getStrategy($type);

		return $strategy->calculate($order);
	}
}