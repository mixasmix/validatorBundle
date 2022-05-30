<?php

namespace App\Validation\Constraints;

use DateTime;
use DateTimeInterface;
use Exception;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class LessThanDateValidator extends ConstraintValidator
{
    /**
     * @param string     $value
     * @param Constraint $constraint
     *
     * @throws Exception
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!empty($value)) {
            try {
                /**
                 * @var DateTimeInterface
                 */
                $date = $constraint->value;

                if (empty($date)) {
                    $this->context->buildViolation($constraint->message)
                        ->setParameter('{{ string }}', $date->format('d.m.Y'))
                        ->addViolation();
                }

                $validationDate = new DateTime($value);

                if (1 == $validationDate->diff($date)->invert) {
                    $this->context->buildViolation($constraint->message)
                        ->setParameter('{{ string }}', $date->format('d.m.Y'))
                        ->addViolation();
                }
            } catch (Exception $exception) {
                $this->context->buildViolation('Некорректный формат даты')->addViolation();
            }
        }
    }
}
