<?php

namespace TestDeliveryCost;

class Order
{
	private float $weight = 0.0;

	public function getWeight(): float
	{
		return $this->weight;
	}

	public function setWeight(float $weight): void
	{
		if ($weight <= 0) {
			throw new \InvalidArgumentException('Вес должен быть положительным!');
		}

		$this->weight = $weight;
	}
}