<?php

namespace Mixasmix\ValidationBundle\Validation\Constraints;

use Mixasmix\ValidationBundle\Exception\ValidationException;
use Mixasmix\ValidationBundle\Helper\ValidationHelper;
use Symfony\Component\Validator\Constraint;

final class SnilsConstraintValidator extends AbstractConstraintValidator
{
    /**
     * Паттерн проверки снилса с пробелом
     */
    private const SPACE_PATTERN = '/^\d{3}-\d{3}-\d{3} \d{2}$/';

    /**
     * Паттерн проверки снилса с тире
     */
    private const DASH_PATTERN = '/^\d{3}-\d{3}-\d{3}-\d{2}$/';

    /**
     * @param mixed      $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        if (null === $value || '' === $value) {
            return;
        }

        //если есть пробел
        $isSpace = (bool) strpos($value, ' ');
        //если есть тире
        $isDash = (bool) strpos($value, '-');

        try {
            ValidationHelper::isEmpty($value);

            if ($isSpace && $isDash) {
                ValidationHelper::isPattern($value, self::SPACE_PATTERN);
                $length = 14;
            } elseif (!$isSpace && $isDash) {
                ValidationHelper::isPattern($value, self::DASH_PATTERN);
                $length = 14;
            } else {
                ValidationHelper::isPattern($value);
                $length = 11;
            }

            ValidationHelper::isCorrectLength($value, $length);

            $value = str_replace([' ', '-'], '', $value);

            if (ValidationHelper::snilsCheckSum($value) !== (int) substr($value, -2)) {
                $this->context->buildViolation('Контрольная сумма СНИЛС не совпадает!')->addViolation();
            }
        } catch (ValidationException $exception) {
            $this->context->buildViolation($exception->getMessage())->addViolation();
        }
    }
}
