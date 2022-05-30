<?php

namespace Mixasmix\ValidationBundle\Validation\Constraints;

final class KSConstraint extends AbstractConstraint
{
    /**
     * Имя параметра
     */
    protected const NAME = 'Корреспондентский счет';

    /**
     * @var string | null
     */
    public ?string $currency;

    /**
     * @var string
     */
    public string $key;

    /**
     * @param mixed $options
     */
    public function __construct($options = null)
    {
        if (is_string($options)) {
            $this->key = $options;
        }

        $this->currency = $options['currency'] ?? null;


        parent::__construct($options);
    }
}
