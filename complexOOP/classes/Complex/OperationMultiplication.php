<?php

namespace Complex;

class OperationMultiplication implements Operation
{
	/**
	 * Выполнить операцию
	 *
	 * @param Number $complexA
	 * @param Number $complexB
	 * @return Number
	 */
	public function execute(Number $complexA, Number $complexB)
	{
		$reA = $complexA->getReal();
		$reB = $complexB->getReal();
		$imA = $complexA->getImaginary();
		$imB = $complexB->getImaginary();

		$re = ($reA * $reB - $imA * $imB);
		$im = ($reA * $imB + $reB * $imA);

		return new Number($re, $im);
	}

	/**
	 * Получить название операции
	 *
	 * @return string
	 */
	public function getTitle()
	{
		return 'Умножение';
	}
}