<?php

namespace TestDeliveryCost\Delivery\Strategies;

use TestDeliveryCost\Order;

interface DeliveryStrategyInterface
{
	public function calculate(Order $order): float;
}