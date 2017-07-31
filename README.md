### Cart

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/agoalofalife/cart/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/agoalofalife/cart/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/agoalofalife/cart/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/agoalofalife/cart/?branch=master)
[![License](https://poser.pugx.org/agoalofalife/cart/license)](https://packagist.org/packages/agoalofalife/cart)
[![Build Status](https://scrutinizer-ci.com/g/agoalofalife/cart/badges/build.png?b=master)](https://scrutinizer-ci.com/g/agoalofalife/cart/build-status/master)


**Что это такое?**

 Это простой пакет для хранение товаров в корзине магазина. 
 Он не навязывает свою структуру и вы всегда сможете поменять тип хранения, давай рассмотрим его уже!

- [Требования](#Required)
- [Установка](#Installation)
- [Установка конфигураций](#Configuration)
- [Драйвера](#Drivers)
     - [Использование](#Use)
- [Написание своего драйвера](#CustomDriver)
- [Интеграция с Laravel](#Laravel)



<a name="Required"></a>

```
Обратите внимание, что для использования необходимо иметь :
- Mysql не ниже версии 5.7.8 (Если вы используете драйвер по-умолчанию)
- Версия PHP не ниже 7.1
```

<a name="Installation"></a>
**Установка**

Достаточно выполнить 

```
composer require agoalofalife/cart
```

<a name="Configuration"></a>
**Установка конфигураций**

В начале было слово..
Каждый раз когда вы создаете класс для корзины, он может иметь свой способ хранения информации (Драйверы)ю

Например , драйвер базы данных имеет настройки подключение название таблицы и тому подобное.
Давайте рассмотрим как мы можем загрузить конфигурации:

```
$kernel = new \Cart\Kernel();
$kernel->bootstrapping();
$kernel->loadConfiguration((new \Cart\SourcesConfigurations\File(__DIR__ . '/config/cart.php')));
```

Метод `loadConfiguration` принимает обьект типа `SourceConfiguration`, вы можете использовать класс `File` по умолчанию передав в конструктор путь до файла, или написать свой класс , реализующий `SourceConfiguration`.

```
   // Сервис - провайдеры для настройки ваших драйверов
    'services' => [
        Cart\ServiceProviders\DatabaseServiceProviders::class,
        \Cart\ServiceProviders\RedisServiceProvider::class,
    ],
    'drivers' => [
    // конфигурации в зависимости от драйвера
        'database' => [
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'test',
            'username'  => 'test',
            'password'  => 'test',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => ''
        ],
        'redis' => [
            'prefix' => 'cart',
            'scheme' => 'tcp',
            'host'   => '127.0.0.1',
            'port'   => 6379,
        ]
    ],
    // выбранный текущий драйвер
    'storage' => \Cart\Drivers\DatabaseDriver::class
```

Сверху показан пример структуры конфигурации. 
`storage` и `services` являются обязательными полями, `drivers` зависит от настройки вашего драйвера.
На данном этапе вы можете скопировать этот пример и внести свои настройки.

<a name="Drivers"></a>
**Драйвера**
На данный момент поддерживается два типа драйвера : Redis и база данных.

<a name="Use"></a>
**Использование**
Какой драйвер вы будете использовать зависит от настройки в файле конфигурации:
```
    'storage' => \Cart\Drivers\DatabaseDriver::class
```
Теперь клиентский код:
```
$kernel = new \Cart\Kernel();
$kernel->bootstrapping();
$kernel->loadConfiguration((new \Cart\SourcesConfigurations\File(__DIR__ . '/config/cart.php')));
$kernel->loadServiceProvider();
// получение драйвера
$storage = $kernel->getStorage();
```

После того как драйвер получен мы можем добавлять товар в корзину,  удалять его, менять кол -во и очищать корзину полностью.

```
// Добавление товара в корзину
// id и user_id обязательные поля, так как это индексы по которым происходят другие операции
// вы в праве добавить больше данных..
$storage->add(['id' => 3, 'user_id' => 1]);

// очистить корзину передав user_id
$storage->clear(1);

// изменить кол -во 
$storage->change(['id' => 5, 'user_id' => 1, 'count' => 0])

// удалить конкретный товар из корзины 
$storage->remove(['id' => 2, 'user_id' => 1]);
```

Помимо этого так же есть возможность изменить цену товара (Например скидка).

```
// Поле price обязательно.
// Сделать фиксированную скидку
$storage->discount(new \Cart\DiscountStrategy\FixDiscountStrategy(100), ['id' => 3, 'user_id' => 1, 'price' => 200]);

// Осуществить скидку по проценту
$storage->discount(new \Cart\DiscountStrategy\PercentageStrategy(20), ['id' => 3, 'user_id' => 1, 'price' => 200]);

```
<a name="Laravel"></a>
**Интеграция с Laravel**

Для того чтобы интегрировать библиотеку в Laravel необходимо :

- Установить через composer

-  Выполнить команду :
```
./vendor/bin/cart migrate:laravel   
```
 Тем самым скопировать файл конфигурации и миграцию в исходные папки Laravel

- Получить driver из контейнера и работать с ним (Предварительно установив все настройки и выполнив миграцию , если используется драйвер базы данных.)

```
app('cart')-> ...
```
