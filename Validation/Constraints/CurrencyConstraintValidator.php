<?php

namespace Mixasmix\ValidationBundle\Validation\Constraints;

use Mixasmix\ValidationBundle\Enum\Currency;
use Mixasmix\ValidationBundle\Exception\ValidationException;
use Mixasmix\ValidationBundle\Helper\ValidationHelper;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class CurrencyConstraintValidator extends AbstractConstraintValidator
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

        if (!$constraint instanceof CurrencyConstraint) {
            throw new UnexpectedTypeException($constraint, CurrencyConstraint::class);
        }

        try {
            ValidationHelper::checkCurrency($value, $constraint->currency ?? Currency::RUB);
        } catch (ValidationException $exception) {
            $this->context->buildViolation($exception->getMessage())->addViolation();
        }
    }
}
