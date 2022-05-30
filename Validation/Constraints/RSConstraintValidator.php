<?php

namespace Mixasmix\ValidationBundle\Validation\Constraints;

use Mixasmix\ValidationBundle\Exception\ValidationException;
use Mixasmix\ValidationBundle\Helper\ValidationHelper;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class RSConstraintValidator extends AbstractConstraintValidator
{
    /**
     * @param mixed      $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof RSConstraint) {
            throw new UnexpectedTypeException($constraint, RSConstraint::class);
        }

        try {
            if (!empty($constraint->key) && is_string($constraint->key)) {
                $key = ValidationHelper::getValueByKey(
                    $this->context->getRoot(),
                    $constraint->key,
                    $this->context->getPropertyPath(),
                );

                ValidationHelper::isEmpty($key);
                ValidationHelper::isPattern($key);
                ValidationHelper::isCorrectLength($key, 9);

                //Составляем 23-значное число из 3-х последних цифр БИК и расчетного счета
                $bikKs = substr($key, -3) . $value;

                if (ValidationHelper::checkSumRsKs($bikKs) % 10 !== 0) {
                    $this->context->buildViolation(
                        'Расчетный счет {{value}} не соответствует БИК {{bik}}'
                    )->setParameters(['{{value}}' => $value, '{{bik}}' => $key])->addViolation();
                }
            }

            ValidationHelper::isEmpty($value);
            ValidationHelper::isPattern($value);
            ValidationHelper::isCorrectLength($value, 20);


            if (!is_null($constraint->currency)) {
                ValidationHelper::checkCurrency($value, $constraint->currency);
            }
        } catch (ValidationException $exception) {
            $this->context->buildViolation($exception->getMessage())->addViolation();
        }
    }
}
