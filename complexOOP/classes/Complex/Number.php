<?php

namespace Complex;

class Number
{
	/** @var float Действительная часть */
	private $real;

	/** @var float Мнимая часть */
	private $imaginary;

	public function __construct(float $real, float $imaginary)
	{
		$this->real = $real;
		$this->imaginary = $imaginary;
	}

	public function __toString()
	{
		$expression = '';
		if ($this->imaginary >= 0)
			$expression = sprintf('z = %s + %si', $this->real, $this->imaginary);
		else
			$expression = sprintf('z = %s - %si', $this->real, abs($this->imaginary));

		return $expression;
	}

	public function getReal()
	{
		return $this->real;
	}

	public function getImaginary()
	{
		return $this->imaginary;
	}

	public function setReal(float $real)
	{
		$this->real = $real;
	}

	public function setImaginary(float $imaginary)
	{
		$this->imaginary = $imaginary;
	}

	/**
	 * Сравнение чисел
	 *
	 * @param Number $number
	 * @return bool
	 */
	public function isEqual(Number $number)
	{
		return (
			($this->getReal() == $number->getReal()) &&
			($this->getImaginary() == $number->getImaginary())
		);
	}

}