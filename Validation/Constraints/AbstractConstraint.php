<?php

namespace Mixasmix\ValidationBundle\Validation\Constraints;

use Symfony\Component\Validator\Constraint;

abstract class AbstractConstraint extends Constraint
{
    /**
     * Название параметра
     */
    protected const NAME = 'name';

    /**
     * @var string
     */
    public string $message;

    /**
     * @param mixed $options
     */
    public function __construct($options = null)
    {
        $this->message = sprintf(
             '%s должен быть длиной {{ string }} символов',
            static::NAME ?? 'Параметр',
        );

        parent::__construct($options);
    }

    /**
     * @var mixed
     */
    public $value;

    /**
     * @return string
     */
    public function getDefaultOption(): string
    {
        return 'value';
    }
}
