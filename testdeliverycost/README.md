## Тестовое задание

### Переписать class DeliveryCalculator
Применил паттер стратегия. Под каждую доставку свой класс реализующий интерфейс   
public function calculate(Order $order): float;

Чтоб интересней было, добавил аттрибуты к классам стратегиям и класс, который по входной строке находит нужный класс 
стратегию. В аттрибутах стратегии прописывается строка доставки "courier", "pickup" и тд.  

Таким образом, достаточно просто добавить новый класс в lib/Delivery/Strategies и.. все! Новая логика появилась, 
не надо никуда больше лезть, полное соблюдение O из SOLID.

**p.s.** Можно без аттрибутов сделать было проще, через call_user_func_array так-как имя класса содержит название метода 
доставки. Метод calculate переделать в статический и вызов примерно:

```php
<?php

$order = new \TestDeliveryCost\Order();
$order->setWeight(5);

$type = 'courier';
$cost = call_user_func_array([
        'TestDeliveryCost\\Delivery\\Strategies\\'.ucfirst($type).'DeliveryStrategy', 
        'calculate'
    ],
    [$order]
);
```

**p.p.s.** Еще в духе современного PHP оформить DeliveryCalculator как сервис и внедрить через конструктор зависимость [DeliveryStrategyFactory.php](lib%2FDelivery%2FDeliveryStrategyFactory.php)