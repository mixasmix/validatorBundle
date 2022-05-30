<?php

namespace Mixasmix\ValidationBundle\Validation\Constraints;

final class RSConstraint extends AbstractConstraint
{
    /**
     * Имя параметра
     */
    protected const NAME = 'Расчетный счет';

    /**
     * @var string | null
     */
    public ?string $currency;

    /**
     * @var string | bool
     */
    public $key;

    /**
     * @param mixed $options
     */
    public function __construct($options = 'bik')
    {
        if (is_string($options)) {
            $this->key = $options;
        }

        $this->currency = $options['currency'] ?? null;

        parent::__construct($options);
    }
}
