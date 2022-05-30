<?php

namespace Mixasmix\ValidationBundle\Validation\Constraints;

use Symfony\Component\Validator\Constraint;

class OneOf extends AbstractConstraint
{
    /**
     * @var array | Constraint[]
     */
    public array $constraints = [];

    /**
     * @param mixed $constraints
     */
    public function __construct($constraints = null)
    {
        parent::__construct($constraints ?? []);
    }
}
