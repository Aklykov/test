<?php

namespace Complex;

interface Operation
{
	public const ADDITION = 'OperationAddition';
	public const SUBTRACTION = 'OperationSubtraction';
	public const MULTIPLICATION = 'OperationMultiplication';
	public const DIVISION = 'OperationDivision';

	/**
	 * Выполнить операцию
	 *
	 * @param Number $complexA
	 * @param Number $complexB
	 * @return Number
	 */
	public function execute(Number $complexA, Number $complexB);

	/**
	 * Получить название операции
	 *
	 * @return string
	 */
	public function getTitle();
}