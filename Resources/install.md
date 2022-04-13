Установка
===================

Сперва напиши в `composer.json` своего проекта следующие строчки:

    "repositories": [
        {
            "type": "gitlab",
            "url": "https://github.com/mixasmix/validatorBundle",
            "branch": "master"
        }
    ]

а также:
        "require": {
            "mixasmix/validation-bundle": "*"
        }

При запуске `composer update` консоль попросит данные аутентификации в гитлаб.
      