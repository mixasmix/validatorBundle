<?php

namespace Mixasmix\ValidationBundle\Validation\Constraints;

use Mixasmix\ValidationBundle\Exception\ValidationException;
use Mixasmix\ValidationBundle\Helper\ValidationHelper;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

abstract class AbstractConstraintValidator extends ConstraintValidator
{
    /**
     * @param mixed      $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        if (null === $value || '' === $value) {
            return;
        }

        try {
            ValidationHelper::isEmpty($value);
            ValidationHelper::isPattern($value);

            if (!empty(static::LENGTH)) {
                ValidationHelper::isCorrectLength($value, static::LENGTH);
            }
        } catch (ValidationException $exception) {
            $this->context->buildViolation($exception->getMessage())->addViolation();
        }
    }
}
