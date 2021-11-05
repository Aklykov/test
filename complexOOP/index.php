<?php
namespace Complex;

// загружаем классы
spl_autoload_register(function ($class) {
	$class = str_replace('\\', '/', $class);
	include 'classes/' . $class . '.php';
});

// массив тестов
$testsInput = [
	[1, 3, 4, -5, Operation::ADDITION, 5, -2],
	[5, -6, -3, 2, Operation::SUBTRACTION, 8, -8],
	[2, 3, -1, 1, Operation::MULTIPLICATION, -5, -1],
	[-2, 1, 1, -1, Operation::DIVISION, -1.5, -0.5],
	[1, 3, 4, -5, Operation::ADDITION, 5, -4],
];

foreach ($testsInput as $testInput)
{
	$testResult = Test::assert($testInput);
	if ($testResult->isSuccess())
	{
		echo 'Тест пройден!<br>';
		echo $testResult->getReport();
	}
	else
	{
		echo 'Тест не пройден!<br>';
		echo $testResult->getReport();
	}
}