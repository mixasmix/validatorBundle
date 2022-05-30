<?php

namespace Mixasmix\ValidationBundle\Validation\Constraints;

use Mixasmix\ValidationBundle\Exception\ValidationException;
use Mixasmix\ValidationBundle\Helper\ValidationHelper;
use Symfony\Component\Validator\Constraint;

final class OgrnConstraintValidator extends AbstractConstraintValidator
{
    /**
     * Длина параметра
     */
    protected const LENGTH = 13;
}
