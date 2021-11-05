<?php

namespace Complex;

class OperationDivision implements Operation
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

		$re = ($reA * $reB + $imA * $imB) / ($reB ** 2 + $imB ** 2);
		$im = ($reB * $imA - $reA * $imB) / ($reB ** 2 + $imB ** 2);

		return new Number($re, $im);
	}

	/**
	 * Получить название операции
	 *
	 * @return string
	 */
	public function getTitle()
	{
		return 'Деление';
	}
}