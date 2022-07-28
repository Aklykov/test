# Решение
При установке модуля будет создан тип ИБ + 2 ИБ со свойствами и демо-данными.
Проверка работы контроллера

```
BX.ajax.runAction('aklykov:reviewscity.api.reviews.getList', {
    data: {
        limit: 10,
        page: 1,
    }
}).then(function (response) {
    console.log(response);		
}, function (response) {
    console.log(response);		
});
```