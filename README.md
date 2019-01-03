[![Latest Stable Version](https://poser.pugx.org/adv/bitrix-event-plugin/v/stable)](https://packagist.org/packages/adv/bitrix-event-plugin)
[![Total Downloads](https://poser.pugx.org/adv/bitrix-event-plugin/downloads)](https://packagist.org/packages/adv/bitrix-event-plugin)
[![Latest Unstable Version](https://poser.pugx.org/adv/bitrix-event-plugin/v/unstable)](https://packagist.org/packages/adv/bitrix-event-plugin)
[![License](https://poser.pugx.org/adv/bitrix-event-plugin/license)](https://packagist.org/packages/adv/bitrix-event-plugin)

Bitrix events plugin
=====================

Allows you to realize some capabilities of the 1C-Bitrix module (install/uninstall permanent events)
as a composer library.
If you want to install your package as module, component or template, please use the [composer installer](https://github.com/composer/installers) with
appropriate type (bitrix-d7-module, bitrix-d7-component, bitrix-d7-template).

Позволяет вам реализовать некоторые возможности модуля 1С-Битрика (такие, как установка/удаление событий)
в рамках вашего пакета.
Если вы хотите установить ваш пакет как модуль, компонент, или шаблон, пожалуйста, используйте [composer installer](https://github.com/composer/installers)
с соответствующим типом (bitrix-d7-module, bitrix-d7-component, bitrix-d7-template).

Installation
------------

To install the latest stable version of this plugin open a console and execute the following command:

```bash
$ composer require adv/bitrix-event-plugin
```

Для установки последней стабильной версии просто введите команду:

```bash
$ composer require adv/bitrix-event-plugin
```

Usage
-------

```json
{
    "extra": {
        "adv/bitrix-event-plugin": {
            "events": {
                "myGreatEvent": {
                    "event":"OnSaleOrderSave",
                    "module":"sale",
                    "class":"\\My\\Event\\HandlerClass",
                    "method":"myMethod",
                    "sort":100,
                    "version":1
                }
            }
        }
    }
}
```

Also, in base required you should define bitrix document root directory:

```json
{
  "extra": {
    "bitrix-dir": "web"
  }
}
``` 

License
-------
This component is under the MIT license. See the complete license in the [LICENSE] file.

Reporting an issue or a feature request
---------------------------------------
You'r welcome!