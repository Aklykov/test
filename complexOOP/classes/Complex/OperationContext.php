<?php

namespace Complex;

class OperationContext
{
	/** @var Operation */
	private $operation;

	public function __construct(Operation $operation)
	{
		$this->setOperation($operation);
	}

	/**
	 * Установить тип операции
	 *
	 * @param Operation $operation
	 */
	public function setOperation(Operation $operation)
	{
		$this->operation = $operation;
	}

	/**
	 * Выполнить операцию
	 *
	 * @param Number $complexA
	 * @param Number $complexB
	 * @return Number
	 */
	public function executeOperation(Number $complexA, Number $complexB)
	{
		return $this->operation->execute($complexA, $complexB);
	}

	/**
	 * Получить название операции
	 *
	 * @return string
	 */
	public function getTitle()
	{
		return $this->operation->getTitle();
	}
}