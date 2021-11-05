<?php

namespace Complex;

class OperationSubtraction implements Operation
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
		$re = $complexA->getReal() - $complexB->getReal();
		$im = $complexA->getImaginary() - $complexB->getImaginary();

		return new Number($re, $im);
	}

	/**
	 * Получить название операции
	 *
	 * @return string
	 */
	public function getTitle()
	{
		return 'Вычитание';
	}
}