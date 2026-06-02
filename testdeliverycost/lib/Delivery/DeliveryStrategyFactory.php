<?php

namespace TestDeliveryCost\Delivery;
use TestDeliveryCost\Delivery\Attribute\DeliveryType;
use TestDeliveryCost\Delivery\Strategies\DeliveryStrategyInterface;

class DeliveryStrategyFactory
{
	private static array $strategies = [];

	public static function getStrategy(string $type): DeliveryStrategyInterface
	{
		// Если стратегии уже закешированы, используем их
		if (isset(self::$strategies[$type])) {
			return new (self::$strategies[$type])();
		}

		// Ищем все классы в текущем namespace, реализующие DeliveryStrategyInterface и помеченные DeliveryType
		$classes = self::findDeliveryStrategyClasses();

		foreach ($classes as $class) {
			$reflection = new \ReflectionClass($class);

			// Проверяем наличие атрибута DeliveryType
			$attributes = $reflection->getAttributes(DeliveryType::class);
			if (!empty($attributes)) {
				foreach ($attributes as $attribute) {
					/** @var DeliveryType $deliveryType */
					$deliveryType = $attribute->newInstance();

					if ($deliveryType->type === $type) {
						self::$strategies[$type] = $class;
						return new $class();
					}
				}
			}
		}

		throw new \InvalidArgumentException("Unknown delivery type: $type");
	}

	private static function findDeliveryStrategyClasses(): array
	{
		$classes = [];
		$reflection = new \ReflectionClass(DeliveryStrategyInterface::class);
		$namespace = $reflection->getNamespaceName();
		$directory = __DIR__ . DIRECTORY_SEPARATOR . 'Strategies';

		// Находим все PHP файлы в текущем каталоге и его подкаталогах
		$iterator = new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS)
		);

		/** @var \SplFileInfo $file */
		foreach ($iterator as $file) {
			if ($file->isFile() && $file->getExtension() === 'php') {
				$className = str_replace('.php', '', $file->getFilename());
				$fullyQualifiedClassName = $namespace ? "$namespace\\$className" : $className;
				try {
					$reflectionClass = new \ReflectionClass($fullyQualifiedClassName);

					// Проверяем реализацию интерфейса и наличие атрибута
					if (
						$reflectionClass->implementsInterface(DeliveryStrategyInterface::class) &&
						$reflectionClass->getAttributes(DeliveryType::class)
					) {
						$classes[] = $fullyQualifiedClassName;
					}
				} catch (\ReflectionException $e) {
					continue;
				}
			}
		}

		return $classes;
	}
}