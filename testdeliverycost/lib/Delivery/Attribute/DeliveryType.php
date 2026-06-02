<?php

namespace TestDeliveryCost\Delivery\Attribute;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class DeliveryType
{
	public function __construct(public string $type) {}
}