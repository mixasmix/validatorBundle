<?php

namespace Mixasmix\ValidationBundle\Validation\Constraints;

use Mixasmix\ValidationBundle\Exception\ValidationException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class BooleanValidator extends AbstractConstraintValidator
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

        if (!$constraint instanceof PassportConstraint) {
            throw new UnexpectedTypeException($constraint, Boolean::class);
        }

        try {
            if (is_string($value)) {
                if ('true' === $value) {
                    $value = true;
                } elseif ('false' === $value) {
                    $value = false;
                }
            }

            if (!is_bool($value)) {
                $this->context->buildViolation($constraint->message)->addViolation();
            }
        } catch (ValidationException $exception) {
            $this->context->buildViolation($exception->getMessage())->addViolation();
        }
    }
}
