Описание moysklad

Стояла задача синхронизовать обмен заказами из RetailCRM в МойСклад через ИМ на Битрикс.
Ключевые классы
Client.php с помощью которого мы делаем запросы в МС
Entity.php является абстрактным классом от которого наследуются все сущности МойСклад
В нем описаны все основные запросы и действия
При наследовании класса достаточно переопределить protected static $entity = '/entity/group';
И станет доступен весь набор логики из родителя.

Точкой входа является метод класса Order::createOrderFromRetailCrm($orderCrm)
$orderCrm это массив заказа, получаемый из РетайлСРМ (используется апи модуля отдельного для работы с СРМ)
Внутри этого метода создаются/обновляются все составные объекты заказа и сам заказ.
В момент изменения заказа в РетайлСРМ отправляется запрос в Битрикс, который обновляет заказ в самом Битрикс + делает запрос в МС
Платная доставка создается как отдельная услуга с "плавающей" ценой к заказу.

Также на этом сайте использовались товары комплекты (как из простых товаров, так и из других комплектов)
Была написана рекурсивная логика разбивки их на комплектующие, с разбросом скидки по товарам и отправки в МС.
