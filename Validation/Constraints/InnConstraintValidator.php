<?php

namespace Mixasmix\ValidationBundle\Validation\Constraints;

use Mixasmix\ValidationBundle\Exception\ValidationException;
use Mixasmix\ValidationBundle\Helper\ValidationHelper;
use Symfony\Component\Validator\Constraint;

final class InnConstraintValidator extends AbstractConstraintValidator
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
            ValidationHelper::innCheckSum($value);
        } catch (ValidationException $exception) {
            $this->context->buildViolation($exception->getMessage())->addViolation();
        }
    }
}
