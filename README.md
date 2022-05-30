# Validation Bundle

## Использование:  
Необходимо установить бандл, добавив в composer.json своего проекта

	   "repositories": [  
		     { 
			     "type": "gitlab",
			     "url": "https://gitlab.simple-bank.ru/tools/bundles/validation-bundle",
			     "branch": "master" 
			} 
		] 
и

	 "config": {
		 "gitlab-domains": ["gitlab.simple-bank.ru"],
			"secure-http": false,
	 }  
Потом в консоли: `composer require fingeneers/validation-bundle`  
При запуске `composer update` консоль попросит данные аутентификации в гитлаб.  
В файле service.yaml:

	  Mixasmix\ValidationBundle\Service\ValidationService:
			  autowire: true
			  autoconfigure: true 
После этого бандл готов к работе. Для пользования бандлом необходимо:

##ИНН
Для валидации ИНН использовать конструкцию:

	  'inn' => [new InnConstraint()]

##БИК
Для валидации БИК:

	 'bik' => [new BikConstraint()]

##Валюта расчетного/корреспондентского счета
Для валидации валюты счета:

	'rs' => [new CurrencyConstraint()]
В конструктор можно передать валюту счета в международном формате. См. Enum::Cunnercy,            по умолчанию российские рубли.  

##КПП
Для валидации КПП:

	'kpp' => [new KppConstraint()]

#Корпоративный счет (КС)
Для валидации Корпоративного счета:

	'ks' => [new KSConstraint()]
По умолчанию проверка контрольной суммы по БИК отключено. Для включения проверки контрольной суммы необходимо передать в конструктор имя поля БИК и чтобы значение БИК лежало на одном уровне, например:

	 [ 
		 'requisites' => [
			 'bik' => [new BikConstraint()],
			 'ks' => [new KSConstraint('bik')],
		], 
	], 
Так же можно проверить валюту счета, передав в конструктор массив параметров :

	 [ 
		 'requisites' => [
			  'myBik' => [new BikConstraint()],
			  'ks' => [new KSConstraint(['key' => 'myBik', 'currency' => Currency::RUB])],
		  ],
	  ],

##ОГРН
Для валидации ОГРН:

	'ogrn' => [new OgrnConstraint()]

##ОГРНИП
Для валидации ОГРНИП:

	'ogrnip' => [new OgrnipConstraint()]

##Снилс
Для валидации Снилс:

	'snils' => [new SnilsConstraint()]`

##Расчетный счет (РС) 
Для валидации Расчетного счета: По умолчанию проверка контрольной суммы по БИК включено. Для отключения проверки контрольной суммы необходимо передать в коструктор false. При включенной проверке контрольной суммы необходимо чтобы значение БИК лежало на одном уровне, например:

	[ 
		'requisites' => [
			'bik' => [new BikConstraint()],
			'rs' => [new RSConstraint()],
		], 
	],
Отключение проверки контрольной суммы:

	 [ 
		 'requisites' => [ 
			 'bik' => [new BikConstraint()],
			 'rs' => [new RSConstraint(false)],
		 ],
	 ],
или массив значений:

	[
		'requisites' => [ 
			'bik' => [new BikConstraint()],
			'rs' => [new RSConstraint(['key' => false])],
		],
	],
Так же можно проверить валюту счета, передав в конструктор массив параметров:

	[ 
		'requisites' => [ 
			'myBik' => [new BikConstraint()], 
			'rs' => [new RSConstraint(['key' => 'myBik', 'currency' => Currency::RUB])],
		],
	],

#Паспорт
Для валидации паспорта:

    'passport' => [
        new PassportConstraint(
            [
                'full_name' => 'passport_name',
                'issue_date' => 'parrport_issue_date',
                'birth_day' => 'citizen_birth_day',
                'division_code' => 'passport_division_code',
                'division_name' => 'passport_division_name',
                'series' => 'passport_series',
                'number' => 'passport_number',
            ],
        ),
        new Assert\Collection([
            'fields' => [
                'passport_name' => $this->getNotBlank(),
                'parrport_issue_date' => $this->getNotBlank(),
                'citizen_birth_day' => $this->getNotBlank(),
                'passport_division_code' => $this->getNotBlank(),
                'passport_division_name' => $this->getNotBlank(),
                'passport_series' => $this->getNotBlank(),
                'passport_number' => $this->getNotBlank(),
            ],
        ]),
    ],
Необходимо конструктор `PassportConstraint` передать массив значений и явно указать названия валидируемых полей паспорта.
Правило нужно применять к родительскому элементу, содержащему поля паспорта. Если не передать в массиве какое-то из значений, 
то правило валидации по этому значению работать не будет, например если не передать `number` то в правилах валидации не будет
проверки на длину и корректность номера паспорта.

##Дата
###Дата не меньше чем указанная
Для проверки даты:
    
    [
        'date' => [
            new GreaterThanDate(
                'value' => new DateTimeImmutable(),
                'mesasge' => 'Дата должна быть из будущего или настоящего'
            ),
        ]
    ]

###Дата не больше чем указанная
Для проверки даты:

    [
        'date' => [
            new LessThanDate(
                'value' => new DateTimeImmutable(),
                'mesasge' => 'Дата должна быть из прошлого'
            ),
        ]
    ]

##Для проверки булева типа
Для проверки булева типа. Если значение может содержать строку, соответствующую булеву типу, например `'false'`:

    [
        'value' => [new Boolean()]
    ]

##"один из",
В данное правило можно передать много других правил, и если хоть одно правило прошло валидацию - валидация будет считаться пройденной успешно

    [
        'full_name' => new OneOf([
            new Assert\Type(['type' => 'int']),
            new Assert\Type(['type' => 'string']),
        ])
    ]
