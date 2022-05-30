<?php

namespace Mixasmix\ValidationBundle\Validation\Constraints;

use Mixasmix\ValidationBundle\Exception\ValidationException;
use Mixasmix\ValidationBundle\Helper\ValidationHelper;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class KSConstraintValidator extends AbstractConstraintValidator
{
    /**
     * @param mixed      $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof KSConstraint) {
            throw new UnexpectedTypeException($constraint, KSConstraint::class);
        }

        try {
            if (!is_null($constraint->key)) {
                $key = ValidationHelper::getValueByKey(
                    $this->context->getRoot(),
                    $constraint->key,
                    $this->context->getPropertyPath(),
                );

                ValidationHelper::isEmpty($key);
                ValidationHelper::isPattern($key);
                ValidationHelper::isCorrectLength($key, 9);

                //Составляем 23-значное число из нуля, 5-й и 6-й цифр БИК и корреспондентского счета.
                $bikKs = '0' . substr($key, -5, 2) . $value;

                if (!is_null($constraint->currency)) {
                    ValidationHelper::checkCurrency($value, $constraint->currency);
                }

                if (ValidationHelper::checkSumRsKs($bikKs) % 10 !== 0) {
                    $this->context->buildViolation(
                        'Корреспондентский счет {{value}} не соответствует БИК {{bik}}'
                    )->setParameters(['{{value}}' => $value, '{{bik}}' => $key])->addViolation();
                }
            }

            ValidationHelper::isEmpty($value);
            ValidationHelper::isPattern($value);
            ValidationHelper::isCorrectLength($value, 20);
        } catch (ValidationException $exception) {
            $this->context->buildViolation($exception->getMessage())->addViolation();
        }
    }
}
