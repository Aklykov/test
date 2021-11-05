<?php

namespace Complex;

class Test
{
	/**
	 * Проверить утверждение
	 *
	 * @param array $input
	 * @return TestResult
	 */
	public static function assert($input=[])
	{
		$numberA = new Number($input[0], $input[1]);
		$numberB = new Number($input[2], $input[3]);
		$numberAnswer = new Number($input[5], $input[6]);

		$operationClass = '\\'.__NAMESPACE__.'\\' . $input[4];
		$operation = new OperationContext(new $operationClass());
		$numberC = $operation->executeOperation($numberA, $numberB);

		$isSuccess = $numberAnswer->isEqual($numberC);
		$report = '';
		$report .= 'Операция: ' . $operation->getTitle() . '<br>';
		$report .= 'Число A: ' . $numberA . '<br>';
		$report .= 'Число B: ' . $numberB . '<br>';
		$report .= 'Результат: ' . $numberC . '<br>';
		$report .= 'Ответ: ' . $numberAnswer . '<br>';
		$report .= '<hr>';

		return new TestResult($isSuccess, $report);
	}
}