<?php

namespace Mixasmix\ValidationBundle\Validation\Constraints;

use DateTimeImmutable;
use Exception;
use Mixasmix\ValidationBundle\DTO\PassportData;
use Mixasmix\ValidationBundle\Exception\ValidationException;
use Mixasmix\ValidationBundle\Helper\ValidationHelper;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class PassportConstraintValidator extends AbstractConstraintValidator
{
    /**
     * @param mixed      $value
     * @param Constraint $constraint
     *
     * @throws Exception
     */
    public function validate($value, Constraint $constraint): void
    {
        if (null === $value || '' === $value) {
            return;
        }

        if (!$constraint instanceof PassportConstraint) {
            throw new UnexpectedTypeException($constraint, PassportConstraint::class);
        }

        if (!is_null($constraint->series)) {
            $series = ValidationHelper::getValueByKey(
                $this->context->getRoot(),
                $constraint->series,
                $this->patcher($this->context->getPropertyPath(), $constraint->series),
            );
        }

        if (!is_null($constraint->number)) {
            $number = ValidationHelper::getValueByKey(
                $this->context->getRoot(),
                $constraint->number,
                $this->patcher($this->context->getPropertyPath(), $constraint->number),
            );
        }

        if (!is_null($constraint->issueDate)) {
            $issueDate = new DateTimeImmutable(
                ValidationHelper::getValueByKey(
                    $this->context->getRoot(),
                    $constraint->issueDate,
                    $this->patcher($this->context->getPropertyPath(), $constraint->issueDate),
                )
            );
        }

        if (!is_null($constraint->birthDay)) {
            $birthDay = new DateTimeImmutable(
                ValidationHelper::getValueByKey(
                    $this->context->getRoot(),
                    $constraint->birthDay,
                    $this->patcher($this->context->getPropertyPath(), $constraint->birthDay)
                ),
            );
        }

        if (!is_null($constraint->divisionCode)) {
            $divisionCode = ValidationHelper::getValueByKey(
                $this->context->getRoot(),
                $constraint->divisionCode,
                $this->patcher($this->context->getPropertyPath(), $constraint->divisionCode),
            );
        }

        if (!is_null($constraint->divisionName)) {
            $divisionName = ValidationHelper::getValueByKey(
                $this->context->getRoot(),
                $constraint->divisionName,
                $this->patcher($this->context->getPropertyPath(),  $constraint->divisionName),
            );
        }

        if (!is_null($constraint->fullName)) {
            $fullName = ValidationHelper::getValueByKey(
                $this->context->getRoot(),
                $constraint->fullName,
                $this->patcher($this->context->getPropertyPath(), $constraint->fullName),
            );
        }

        try {
            ValidationHelper::passportValidation(
                new PassportData(
                    $series ?? null,
                    $number ?? null,
                    $issueDate ?? null,
                    $birthDay ?? null,
                    $divisionCode ?? null,
                    $divisionName ?? null,
                    $fullName ?? null,
                ),
            );
        } catch (ValidationException $exception) {
            $this->context->buildViolation($exception->getMessage())->addViolation();
        }
    }


    /**
     * Костыль для работы валидатора с родительским элементом
     *
     * @param string $path
     * @param string $propertyName
     *
     * @return string
     */
    private function patcher(string $path, string $propertyName): string
    {
        return sprintf('%s[%s]', $path, $propertyName);
    }
}
