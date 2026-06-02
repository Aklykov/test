<?php

require_once 'autoload.php';

$order = new \TestDeliveryCost\Order();
$order->setWeight(5);

$deliveryCalculator = new \TestDeliveryCost\Delivery\DeliveryCalculator();

// считаем для всех типов доставку
$types = [
	'courier',
	'pickup',
	'post',
	'express',
	'drone',
];

foreach ($types as $type) {
	$cost = $deliveryCalculator->cost($order, $type);
	print_r('<pre>');print_r("weight: 5; type: $type; cost: $cost");print_r('</pre>');
}






