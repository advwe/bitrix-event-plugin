Bitrix events plugin
=====================

Allows you to realize the some capabilities of the 1C-Bitrix module (install/uninstall permanent events)
as a composer library.
If you want to install your plugin as module or component, please use the composer installer with
appropriate type (bitrix-d7-module, bitrix-d7-component, bitrix-d7-template)

Installation
------------

In the future, to install the latest stable version of this component open a console and execute
the following command:


```
$ composer require adv/bitrix-event-plugin
```

Now you can use composer.json 

```json
{
  "require": {
    "adv/bitrix-event-plugin": "*"
  },
  "repositories": [
    {
      "type": "vcs",
      "url": "ssh://git@bitbucket.adv.ru:7999/adv/bitrix-event-plugin.git"
    }
  ]
}
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
            },
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
