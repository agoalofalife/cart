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
- [Драйвера](#Drivers)
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
