<?php

namespace Mixasmix\ValidationBundle\Validation\Constraints;

use Mixasmix\ValidationBundle\Exception\ValidationException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class OneOfValidator extends AbstractConstraintValidator
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

        if (!$constraint instanceof OneOf) {
            throw new UnexpectedTypeException($constraint, OneOf::class);
        }

        $context = $this->context;

        $validator = $context->getValidator()->inContext($context);

        try {
            $counter = 0;

            foreach ($constraint->constraints as $constraint) {
                $counter++;
                //если хоть одна валидация прошла успешно прибиваем цикл
                if (!$validator->validate($value, $constraint)->getViolations()->count()) {
                    break;
                }
            }

            if ($counter === count($constraint->constraints)) {
                $this->context->buildViolation('Значение не соответствует ни одному казанному правилу')->addViolation();
            }
        } catch (ValidationException $exception) {
            $this->context->buildViolation($exception->getMessage())->addViolation();
        }
    }
}
